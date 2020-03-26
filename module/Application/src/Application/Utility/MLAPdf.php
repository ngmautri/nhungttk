<?php
namespace Application\Utility;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MLAPdf extends \TCPDF
{

    // Page header
    public function Header()
    {
        // Logo
        $image_file = ROOT . '/public/images/mascot.gif';

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 10, 'Mascot International (Lao) Sole co., Ltd', 0, false, 'T', 0, '', 0, false, 'M', 'M');
        // Set font
        $this->Ln(3);
        $this->SetFont('helvetica', 'I', 8);
        $this->Write(0, 'VITA Park Specific Economic Zone  Km 22, Vientiane Capital', '', 0, 'L', true, 0, false, false, 0);

        $this->Image($image_file, 180, 5, 15, '', 'GIF', '', 'T', false, 100, '', false, false, 0, false, false, false);

        // Title
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(- 10);
        // Set font
        $this->SetFont('helvetica', 'I', 7);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

