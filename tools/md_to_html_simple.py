import argparse
import html
import re
from pathlib import Path

AR_RE = re.compile(r"[\u0600-\u06FF]")


def is_arabic(text: str) -> bool:
    return AR_RE.search(text or "") is not None


def md_to_html_lines(md_text: str):
    lines = md_text.splitlines()
    out = []
    in_code = False
    code_lang = ""

    def close_code():
        nonlocal in_code, code_lang
        if in_code:
            out.append("</code></pre>")
            in_code = False
            code_lang = ""

    for raw in lines:
        line = raw.rstrip("\n")

        if line.strip().startswith("```"):
            fence = line.strip()
            if not in_code:
                in_code = True
                code_lang = fence[3:].strip()
                cls = f"language-{html.escape(code_lang)}" if code_lang else ""
                out.append(f"<pre class=\"code\"><code class=\"{cls}\">")
            else:
                close_code()
            continue

        if in_code:
            out.append(html.escape(line))
            continue

        # Horizontal rule
        if line.strip() == "---":
            out.append("<hr />")
            continue

        # Headings
        if line.startswith("### "):
            out.append(f"<h3>{html.escape(line[4:].strip())}</h3>")
            continue
        if line.startswith("## "):
            out.append(f"<h2>{html.escape(line[3:].strip())}</h2>")
            continue
        if line.startswith("# "):
            out.append(f"<h1>{html.escape(line[2:].strip())}</h1>")
            continue

        # Empty line
        if line.strip() == "":
            out.append("<div class=\"spacer\"></div>")
            continue

        # Bullets (very simple)
        if line.lstrip().startswith("- "):
            content = line.lstrip()[2:]
            dir_attr = "rtl" if is_arabic(content) else "ltr"
            out.append(f"<div class=\"bullet\" dir=\"{dir_attr}\">• {html.escape(content)}</div>")
            continue

        # Numbered list (simple)
        m = re.match(r"\s*(\d+)\.\s+(.*)$", line)
        if m:
            num, content = m.group(1), m.group(2)
            dir_attr = "rtl" if is_arabic(content) else "ltr"
            out.append(f"<div class=\"bullet\" dir=\"{dir_attr}\">{num}. {html.escape(content)}</div>")
            continue

        # Normal paragraph
        dir_attr = "rtl" if is_arabic(line) else "ltr"
        out.append(f"<p dir=\"{dir_attr}\">{html.escape(line)}</p>")

    close_code()
    return out


def build_html(md_text: str, title: str) -> str:
    body_lines = md_to_html_lines(md_text)
    body = "\n".join(body_lines)
    return f"""<!doctype html>
<html lang=\"fr\">
<head>
  <meta charset=\"utf-8\" />
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
  <title>{html.escape(title)}</title>
  <style>
    :root {{
      --text: #111;
      --muted: #444;
      --bg: #fff;
      --border: #ddd;
    }}
    html, body {{ background: var(--bg); color: var(--text); }}
    body {{ font-family: Arial, "Segoe UI", Tahoma, sans-serif; line-height: 1.45; margin: 0; }}
    .page {{ max-width: 900px; margin: 0 auto; padding: 32px 20px; }}
    h1 {{ font-size: 28px; margin: 18px 0 10px; border-bottom: 2px solid var(--border); padding-bottom: 6px; }}
    h2 {{ font-size: 22px; margin: 18px 0 8px; }}
    h3 {{ font-size: 18px; margin: 14px 0 6px; color: var(--muted); }}
    p {{ margin: 6px 0; white-space: pre-wrap; }}
    .bullet {{ margin: 4px 0; padding-left: 14px; white-space: pre-wrap; }}
    hr {{ border: none; border-top: 1px solid var(--border); margin: 14px 0; }}
    .spacer {{ height: 6px; }}
    pre.code {{
      border: 1px solid var(--border);
      background: #fafafa;
      padding: 10px 12px;
      overflow-x: auto;
      white-space: pre;
      direction: ltr;
    }}
    pre.code code {{ font-family: Consolas, "Courier New", monospace; font-size: 12px; }}

    @media print {{
      .page {{ padding: 0; }}
      a {{ color: inherit; text-decoration: none; }}
      h1, h2, h3 {{ break-after: avoid; }}
      pre {{ break-inside: avoid; }}
    }}
  </style>
</head>
<body>
  <div class=\"page\">
{body}
  </div>
</body>
</html>
"""


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--input", required=True)
    ap.add_argument("--output", required=True)
    ap.add_argument("--title", default="Monde Magique — Guide de cours")
    args = ap.parse_args()

    md_path = Path(args.input)
    html_path = Path(args.output)

    md_text = md_path.read_text(encoding="utf-8")
    html_text = build_html(md_text, args.title)
    html_path.write_text(html_text, encoding="utf-8")


if __name__ == "__main__":
    main()
