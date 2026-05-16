#!/usr/bin/env python3
# -*- coding: utf-8 -*-
from PIL import Image, ImageDraw
import os

# Stage configurations with their themes
stages_config = {
    2: {
        'name': 'Maghreb',
        'subtitle': 'Merveilles du Monde',
        'icon': '🌍',
        'primary': '#D4A574',
        'secondary': '#AA6B45',
        'tertiary': '#F5EDE0',
        'accent': '#8B5A2B'
    },
    3: {
        'name': 'Afrique',
        'subtitle': 'Royaume Sauvage',
        'icon': '🦁',
        'primary': '#8B4513',
        'secondary': '#CD853F',
        'tertiary': '#F5E6D3',
        'accent': '#654321'
    },
    4: {
        'name': 'Europe',
        'subtitle': 'Châteaux & Trésors',
        'icon': '🏰',
        'primary': '#1E90FF',
        'secondary': '#4169E1',
        'tertiary': '#E6F0FF',
        'accent': '#0047AB'
    },
    5: {
        'name': 'Asie',
        'subtitle': 'Palais Dorés',
        'icon': '🏯',
        'primary': '#FF1493',
        'secondary': '#FFD700',
        'tertiary': '#FFE6F0',
        'accent': '#C71585'
    },
    6: {
        'name': 'Amérique du Nord',
        'subtitle': 'Grandeur Nature',
        'icon': '🗽',
        'primary': '#228B22',
        'secondary': '#32CD32',
        'tertiary': '#E6F5E6',
        'accent': '#1a6b1a'
    },
    7: {
        'name': 'Amérique du Sud',
        'subtitle': 'Jungle Mystérieuse',
        'icon': '🦜',
        'primary': '#FF8C00',
        'secondary': '#FFD700',
        'tertiary': '#FFF0E6',
        'accent': '#B35A00'
    },
    8: {
        'name': 'Océanie',
        'subtitle': 'Îles Paradis',
        'icon': '🏝️',
        'primary': '#20B2AA',
        'secondary': '#00CED1',
        'tertiary': '#E0F5F5',
        'accent': '#008B8B'
    },
    9: {
        'name': 'Pôles',
        'subtitle': 'Royaume Glacé',
        'icon': '❄️',
        'primary': '#4169E1',
        'secondary': '#87CEEB',
        'tertiary': '#E6F2FF',
        'accent': '#00008B'
    },
    10: {
        'name': 'Vue Mondiale',
        'subtitle': 'Tour du Monde',
        'icon': '🌎',
        'primary': '#9370DB',
        'secondary': '#20B2AA',
        'tertiary': '#F0E6FF',
        'accent': '#4B0082'
    }
}

def hex_to_rgb(hex_color):
    """Convert hex color to RGB tuple"""
    hex_color = hex_color.lstrip('#')
    return tuple(int(hex_color[i:i+2], 16) for i in (0, 2, 4))

def create_stage_image(stage_num, config):
    """Create a beautiful decorated poster image for a stage"""
    # Larger image size: 600x450 pixels for better quality and detail
    width, height = 600, 450
    
    # Convert hex colors to RGB
    primary_rgb = hex_to_rgb(config['primary'])
    secondary_rgb = hex_to_rgb(config['secondary'])
    tertiary_rgb = hex_to_rgb(config['tertiary'])
    accent_rgb = hex_to_rgb(config['accent'])
    
    # Create gradient background with multiple colors
    image = Image.new('RGB', (width, height), primary_rgb)
    pixels = image.load()
    
    # Create a diagonal gradient background
    for y in range(height):
        for x in range(width):
            # Blend primary and secondary colors based on position
            ratio_y = y / height
            ratio_x = x / width
            
            # Create interesting gradient pattern
            r = int(primary_rgb[0] * (1 - ratio_y * 0.5) + secondary_rgb[0] * ratio_y * 0.5)
            g = int(primary_rgb[1] * (1 - ratio_y * 0.5) + secondary_rgb[1] * ratio_y * 0.5)
            b = int(primary_rgb[2] * (1 - ratio_y * 0.5) + secondary_rgb[2] * ratio_y * 0.5)
            
            pixels[x, y] = (r, g, b)
    
    # Draw decorative elements
    draw = ImageDraw.Draw(image)
    
    # Add decorative border - outer frame
    border_width = 8
    draw.rectangle(
        [(border_width, border_width), (width - border_width, height - border_width)],
        outline=(255, 255, 255), width=border_width
    )
    
    # Add inner decorative frame
    inner_margin = 20
    draw.rectangle(
        [(inner_margin, inner_margin), (width - inner_margin, height - inner_margin)],
        outline=accent_rgb, width=3
    )
    
    # Add colored corner decorations
    corner_size = 40
    draw.ellipse([(10, 10), (10 + corner_size, 10 + corner_size)], fill=tertiary_rgb)
    draw.ellipse([(width - 50, 10), (width - 10, 50)], fill=tertiary_rgb)
    draw.ellipse([(10, height - 50), (50, height - 10)], fill=tertiary_rgb)
    draw.ellipse([(width - 50, height - 50), (width - 10, height - 10)], fill=tertiary_rgb)
    
    # Add decorative lines
    draw.line([(50, 50), (width - 50, 50)], fill=(255, 255, 255), width=2)
    draw.line([(50, height - 50), (width - 50, height - 50)], fill=(255, 255, 255), width=2)
    
    # Add stage number badge (top right)
    badge_x, badge_y = width - 80, 30
    draw.ellipse([(badge_x - 30, badge_y - 30), (badge_x + 30, badge_y + 30)], 
                 fill=accent_rgb, outline=(255, 255, 255), width=3)
    draw.text((badge_x - 15, badge_y - 15), f"{stage_num}", fill=(255, 255, 255))
    
    # Draw main content area - semi-transparent background
    content_top = 80
    content_bottom = height - 80
    
    # Add semi-transparent content background
    overlay = Image.new('RGBA', (width, height), (0, 0, 0, 0))
    overlay_draw = ImageDraw.Draw(overlay)
    
    # Main title with shadow effect
    title_y = 120
    title_text = config['name']
    title_x = width // 2 - len(title_text) * 15
    
    # Title shadow
    draw.text((title_x + 3, title_y + 3), title_text, fill=(0, 0, 0, 80))
    # Title text (white)
    draw.text((title_x, title_y), title_text, fill=(255, 255, 255))
    
    # Subtitle
    subtitle_text = config['subtitle']
    subtitle_x = width // 2 - len(subtitle_text) * 8
    subtitle_y = 175
    draw.text((subtitle_x, subtitle_y), subtitle_text, 
             fill=tertiary_rgb)
    
    # Large centered icon/emoji
    emoji_text = config['icon']
    emoji_x = width // 2 - 40
    emoji_y = height // 2 - 40
    draw.text((emoji_x, emoji_y), emoji_text, fill=(255, 255, 255))
    
    # Add decorative text at bottom
    bottom_text = "Merveilles du Monde ✨"
    bottom_x = width // 2 - len(bottom_text) * 7
    bottom_y = height - 60
    
    # Bottom text shadow
    draw.text((bottom_x + 2, bottom_y + 2), bottom_text, fill=(0, 0, 0, 80))
    # Bottom text
    draw.text((bottom_x, bottom_y), bottom_text, fill=(255, 255, 255))
    
    # Add decorative stars/sparkles around
    sparkle_positions = [
        (100, 100), (500, 120), (150, 350), (500, 350),
        (80, 250), (520, 250)
    ]
    for pos in sparkle_positions:
        draw.text(pos, "✨", fill=tertiary_rgb)
    
    return image

# Create images directory if it doesn't exist
images_dir = "assets/images"
os.makedirs(images_dir, exist_ok=True)

# Generate images for stages 2-10
for stage_num in range(2, 11):
    config = stages_config[stage_num]
    image = create_stage_image(stage_num, config)
    
    # Save the image with high quality
    filename = f"{images_dir}/c{stage_num}.jpg"
    image.save(filename, 'JPEG', quality=95)
    print(f'✅ Created: {filename}')

print('\n🎨 All beautiful poster images created with HD quality!')
