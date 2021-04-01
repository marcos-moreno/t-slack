<?php
$num = 'CMD01-' . date('ymd');
$nom = 'DUPONT Alphonse';
$date = '31/12/' . date('Y');
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
        <div class="textos serie"><span class="bolds">Serie</span>: 179</div>     
        <div class="espaciado">
            <barcode type="C39" value="M015-CL1-224" style="width: 46mm; height: 10mm; font-size: 4mm"></barcode>
        </div>        
    </div>
    <page_footer>
        <div class="textos footers">Fecha Compra: 01/10/2017</div>
    </page_footer>
</page>