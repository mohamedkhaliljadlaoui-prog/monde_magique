/**
 * Génération de certificats PDF et badges
 * Utilise jsPDF pour créer les PDF
 */

class CertificateGenerator {
    constructor() {
        this.loadjsPDF();
    }

    /**
     * Charger jsPDF depuis CDN
     */
    loadjsPDF() {
        if (!window.jspdf) {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload = () => {
                console.log('jsPDF chargé');
            };
            document.head.appendChild(script);
        }
    }

    /**
     * Générer certificat PDF
     */
    async generatePDF(userName, stageName, stageNumber) {
        if (!window.jspdf || !window.jspdf.jsPDF) {
            setTimeout(() => this.generatePDF(userName, stageName, stageNumber), 500);
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4'
        });

        // Couleurs
        const mainColor = [212, 165, 116];  // Marron
        const accentColor = [170, 107, 69];  // Marron foncé

        // Background dégradé (approximé avec rectangles)
        doc.setFillColor(245, 237, 224);
        doc.rect(0, 0, 297, 210, 'F');

        // Bordure élégante
        doc.setDrawColor(...accentColor);
        doc.setLineWidth(3);
        doc.rect(10, 10, 277, 190);

        doc.setDrawColor(...mainColor);
        doc.setLineWidth(1);
        doc.rect(12, 12, 273, 186);

        // Titre
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(32);
        doc.setTextColor(...mainColor);
        doc.text('CERTIFICAT DE RÉUSSITE', 148.5, 40, { align: 'center' });

        // Décoration
        doc.setLineWidth(1);
        doc.setDrawColor(...mainColor);
        doc.line(80, 50, 217, 50);

        // Texte de présentation
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.setTextColor(51, 51, 51);
        doc.text('Ceci certifie que', 148.5, 65, { align: 'center' });

        // Nom de l'utilisateur
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(20);
        doc.setTextColor(...accentColor);
        doc.text(userName.toUpperCase(), 148.5, 80, { align: 'center' });

        // Texte fin
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.setTextColor(51, 51, 51);
        doc.text('a avec succès complété', 148.5, 95, { align: 'center' });

        // Nom du stage
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.setTextColor(...mainColor);
        doc.text(`Stage ${stageNumber}: ${stageName}`, 148.5, 110, { align: 'center' });

        // Plus de texte
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(11);
        doc.setTextColor(51, 51, 51);
        doc.text('dans le programme Monde Magique de découverte de la géographie mondiale.', 148.5, 125, { align: 'center' });

        // Date
        const now = new Date();
        const dateStr = now.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        doc.setFont('helvetica', 'italic');
        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        doc.text(`Délivré le ${dateStr}`, 148.5, 145, { align: 'center' });

        // Cachets/badges (simples)
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(24);
        doc.setTextColor(...accentColor);
        doc.text('🏆', 50, 155, { align: 'center' });
        doc.text('⭐', 130, 155, { align: 'center' });
        doc.text('🎓', 210, 155, { align: 'center' });

        // Pied de page
        doc.setFont('helvetica', 'italic');
        doc.setFontSize(9);
        doc.setTextColor(150, 150, 150);
        doc.text('Monde Magique - Plateforme d\'apprentissage de la géographie', 148.5, 190, { align: 'center' });

        // Télécharger
        const fileName = `Certificat_${stageName}_${userName}_${now.getTime()}.pdf`;
        doc.save(fileName);

        return true;
    }

    /**
     * Créer un badge image (SVG)
     */
    generateBadgeSVG(stageName, stageNumber) {
        const colors = ['#D4A574', '#FF9800', '#2196F3', '#4CAF50', '#9C27B0', '#E91E63', '#FF5722'];
        const color = colors[stageNumber % colors.length];

        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="100" height="100">
                <defs>
                    <filter id="shadow">
                        <feDropShadow dx="2" dy="2" stdDeviation="3" flood-opacity="0.3"/>
                    </filter>
                </defs>
                
                <!-- Cercle externe -->
                <circle cx="100" cy="100" r="95" fill="${color}" opacity="0.2" stroke="${color}" stroke-width="4"/>
                
                <!-- Étoile centrale -->
                <path d="M100,20 L130,80 L190,80 L145,120 L170,180 L100,140 L30,180 L55,120 L10,80 L70,80 Z" 
                      fill="${color}" filter="url(#shadow)"/>
                
                <!-- Texte du stage -->
                <text x="100" y="160" font-size="14" font-weight="bold" fill="${color}" 
                      text-anchor="middle">Étape ${stageNumber}</text>
            </svg>
        `;

        return svg;
    }

    /**
     * Télécharger badge SVG
     */
    downloadBadge(stageName, stageNumber) {
        const svg = this.generateBadgeSVG(stageName, stageNumber);
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Badge_${stageName}.svg`;
        a.click();
        URL.revokeObjectURL(url);
    }

    /**
     * Partager certificat sur réseaux sociaux
     */
    shareOnSocial(platform, userName, stageName, certificateUrl = '') {
        const message = `🎉 J'ai complété le Stage ${stageName} sur Monde Magique! ${certificateUrl}`;
        const encodedMsg = encodeURIComponent(message);

        const urls = {
            whatsapp: `https://wa.me/?text=${encodedMsg}`,
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(certificateUrl)}&quote=${encodedMsg}`,
            twitter: `https://twitter.com/intent/tweet?text=${encodedMsg}`,
            email: `mailto:?subject=Certificat Monde Magique&body=${encodedMsg}`
        };

        if (urls[platform]) {
            window.open(urls[platform], '_blank');
        }
    }
}

window.CertificateGenerator = CertificateGenerator;
window.certificateGenerator = new CertificateGenerator();
