<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
require_once dirname(__FILE__) . '/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    ob_start();
    ?>
    <style type="text/css">
        <!--
        div.zone { border: none; background: #FFFFFF; border-collapse: collapse; margin: 3.4mm 0mm 0mm  2.5mm; font-size: 2.7mm; width: 43mm; }
        .textos{        text-align: center; float: left; font-size: 2.7mm;      }
        .footers{ margin-bottom: 2mm; }
        .borde{ border-bottom:  1px dotted #8c8b8b; height: 4mm; }
        .bolds{ font-weight: bold; }
        .espaciado{ height: 1mm; }
        .espaciadoHead{ height: 1mm; }
        .serie{ height: 6mm;  vertical-align: middle; }

        -->
    </style>
    <page format="50x35" orientation="L"  style="font: arial;">
        <div class="zone">
            <div class="textos borde" ><?php echo $info['producto']; ?></div>        
            <div class="textos serie"><span class="bolds">SKU</span>: <?php echo $info['sku']; ?></div>     
            <div class="espaciado">
                <barcode type="C39" label="none" value="<?php echo $info['numSerie']; ?>" style="width: 46mm; height: 10mm; font-size: 4mm"></barcode>                   
            </div>        
            <div class="textos"><?php echo $info['numSerie']; ?></div>  
        </div>
        <page_footer>
            <div class="textos footers">Fecha Compra: <?php echo $info['fecha']; ?></div>
        </page_footer>
    </page><?php
    $content = ob_get_clean();

    $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', 0);
    $html2pdf->pdf->IncludeJS("print(true);");
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content);
    $html2pdf->output('ticket.pdf');
}
catch (Html2PdfException $e) {
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
