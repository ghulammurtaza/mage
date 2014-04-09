<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 100,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
    
    protected function convertToBarcodeString($toBarcodeString)
    {
        $str = $toBarcodeString;
        $barcode_data = str_replace(' ', chr(128), $str);

        $checksum = 104; # must include START B code 128 value (104) in checksum
        for($i=0;$i<strlen($str);$i++) {
                $code128 = '';
                if (ord($barcode_data{$i}) == 128) {
                        $code128 = 0;
                } elseif (ord($barcode_data{$i}) >= 32 && ord($barcode_data{$i}) <= 126) {
                        $code128 = ord($barcode_data{$i}) - 32;
                } elseif (ord($barcode_data{$i}) >= 126) {
                        $code128 = ord($barcode_data{$i}) - 50;
                }
        $checksum_position = $code128 * ($i + 1);
        $checksum += $checksum_position;
        }
        $check_digit_value = $checksum % 103;
        $check_digit_ascii = '';
        if ($check_digit_value <= 94) {
            $check_digit_ascii = $check_digit_value + 32;
        } elseif ($check_digit_value > 94) {
            $check_digit_ascii = $check_digit_value + 50;
        }
        $barcode_str = chr(154) . $barcode_data . chr($check_digit_ascii) . chr(156);
            
        return $barcode_str;

    }

    /**
     * Return PDF document
     *
     * @param  array $shipments
     * @return Zend_Pdf
     */
    public function getPdf($shipments = array())
    {
        
         /*
        foreach ($shipments as $invoice) {
             //echo "<pre>"; print_r($invoice);
             echo "new <br><br>";
             $order = $invoice->getOrder();
             
             $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();
                foreach ($shipmentCollection as $shipment){
                    // This will give me the shipment IncrementId, but not the actual tracking information.
                    foreach($shipment->getAllTracks() as $tracknum)
                    {
                        $tracknums[]=$tracknum->getNumber();
                        $tracking .= $tracknum->getNumber();
                    }

             }
             $shipping_address = $order->getShippingAddress();
             //echo "<pre>"; print_r($invoice->getData());
             $name = $shipping_address->getFirstname().' '.$shipping_address->getLastname();
             $telephone = $shipping_address->getTelephone();
             $address =  $shipping_address->getStreet();
             $shippingAddress = $address[0].' '.$shipping_address->getCity().', '.$shipping_address->getRegion().', '.$shipping_address->getPostcode();
             $total = $invoice->getBaseGrandTotal();
             $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/fortis/default/';
             $logoSrc = Mage::getStoreConfig('design/header/logo_src');
             $invoiceNo = $invoice->getIncrementId();
             //echo $this->getSkinUrl('images/logo.gif').'<br>';
             //echo $baseUrl.$logoSrc;
             //echo $image = Mage::getStoreConfig('sales/identity/logo', 1);
             //echo $invoice->getShippingAmount();
             //echo "<pre>"; print_r($shipping_address);
             foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                //echo $item->getOrderItemId();
                $quantity = $item->getQty();
                $desc = "Invoice of the book";
                $price = $item->getPrice().'<br>';
                $totatPrice = $item->getBaseRowTotal();
                //echo "<pre>"; print_r($item->getData()); echo "<br>";
                //$shippingId=$order->getShippingAddressId();
                //$address = Mage::getModel('sales/order_address')->load($shippingId);
                //echo $item->getSku().'<br>';
                //echo "<pre>"; print_r($item);
            }
            
        }
        exit;
        */
        
        $invoiceDate = date("Y-j-n");
        $invoiceTime = date("h-i-s");
        $catenate = $invoiceDate.'_'.$invoiceTime;
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Content-type: application/vnd.ms-excel");  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=packingslip$catenate.xlsx"); // à¹à¸¥à¹‰à¸§à¸™à¸µà¹ˆà¸à¹‡à¸Šà¸·à¹ˆà¸­à¹„à¸Ÿà¸¥à¹Œ
        header("Content-Transfer-Encoding: binary ");

        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
        $objPHPExcel->getProperties()->setCategory("Test result file");

        $default_border = array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb'=>'000000')
        );
        $styleArray = array(
            'borders' => array(
            'bottom' => $default_border,
            'left' => $default_border,
            'top' => $default_border,
            'right' => $default_border,
        ),
            'font' => array(
            'bold' => true
            )
        );
        $stylebgColor = array(
           'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb'=>'E1E0F7'),
            )
        );

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'INVOICE #');
        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('A8', 'Address');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D4:E4');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B7:C7');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:C8');
        $objPHPExcel->getActiveSheet()->setCellValue('A9', 'Telephone');
        $objPHPExcel->getActiveSheet()->setCellValue('A12', 'QUANTITY');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B12:C12');
        $objPHPExcel->getActiveSheet()->setCellValue('B12', 'DESCRIPTION');
        $objPHPExcel->getActiveSheet()->setCellValue('D12', 'AMOUNT');
        $objPHPExcel->getActiveSheet()->setCellValue('E12', 'TOTAL AMOUNT');

        foreach ($shipments as $invoice) {
             $order = $invoice->getOrder();
             $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();
                foreach ($shipmentCollection as $shipment){
                    foreach($shipment->getAllTracks() as $tracknum)
                    {
                        $tracknums[]=$tracknum->getNumber();
                        $tracking .= $tracknum->getNumber();
                    }
             }
             $invoiceNo = $invoice->getIncrementId();
             $shipping_address = $order->getShippingAddress();
             $name = $shipping_address->getFirstname().' '.$shipping_address->getLastname();
             $telephone = $shipping_address->getTelephone();
             $address =  $shipping_address->getStreet();
             $shippingAddress = $address[0].' '.$shipping_address->getCity().', '.$shipping_address->getRegion().', '.$shipping_address->getPostcode();
             $total = $invoice->getBaseGrandTotal();
             $objPHPExcel->getActiveSheet()->setCellValue('B1', $invoiceNo);
             $objPHPExcel->getActiveSheet()->setCellValue('B7', $name);
             $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:C9');
             $objPHPExcel->getActiveSheet()->setCellValue('B8', $shippingAddress);
             $objPHPExcel->getActiveSheet()->setCellValue('B9', $telephone);
             $objPHPExcel->getActiveSheet()->setCellValue('D4', $total.' EGP');
             $i = 13;
             foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                $quantity = $item->getQty();
                $desc = "Invoice of the book";
                $price = $item->getBasePrice();
                $totatPrice = $item->getBaseRowTotal();
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $quantity);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("B$i:C$i");
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $desc);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $price);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $totatPrice);
                $i++;
            }
            $shippingAmount = $invoice->getBaseShippingAmount();
            $objPHPExcel->getActiveSheet()->setCellValue('E20', $shippingAmount);
            $objPHPExcel->getActiveSheet()->setCellValue('E22', $total);
        }
        $today = date("j/n/Y");
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B20:C20');
        $objPHPExcel->getActiveSheet()->setCellValue('B20', 'Aramex Shipping Fee');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B21:C21');
        $objPHPExcel->getActiveSheet()->setCellValue('B21', 'Cash on Delivery Fee (if applicable)');
        $objPHPExcel->getActiveSheet()->setCellValue('E21', 0.00);
        $objPHPExcel->getActiveSheet()->setCellValue('B22', 'Books.com.eg, Inc.');
        $objPHPExcel->getActiveSheet()->setCellValue('D22', 'TOTAL: EGP');
        $objPHPExcel->getActiveSheet()->setCellValue('B23', 'support@books.com.eg');
        $objPHPExcel->getActiveSheet()->setCellValue('D23', 'Date:');
        $objPHPExcel->getActiveSheet()->setCellValue('E23', $today);

        
        //Cell Styling --------------------------------------------------------------------
        // Add Font bold and border
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D4:E4')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A12')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B12:C12')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D12')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E12')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B20:C20')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B21:C21')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D22:E22')->applyFromArray($styleArray);
        
        $counter = 6;
        for($k=1; $k<$counter; $k++){
            if($k ==25)
            {
                break;
            }
            if($k == $counter-1){
                $counter = $counter + $k;    
            }    
            $objPHPExcel->getActiveSheet()->getStyle('A'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('E'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells("B$k:C$k");
        }
        
        // Set width of columns
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(26);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(11);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15); 
        
        // Add Backgroud color
        $objPHPExcel->getActiveSheet()->getStyle('A12')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('B12')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D12')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('E12')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('A20:A21')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('B20')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('E20')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('B21')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D20')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D21')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('E21')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D22:E22')->applyFromArray($stylebgColor);

        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/fortis/default/';
        $logoSrc = Mage::getStoreConfig('design/header/logo_src');
        $logoPath = $baseUrl.$logoSrc;
        //$this->getSkinUrl(‘images/sampleimage.jpg’)
        $gdImage = imagecreatefromgif($logoPath);
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(60);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
        $objDrawing->setCoordinates('C3');
        
        //Barcode Script here
        $barcodee = new Barcode();
        $fontSize = 10;   // GD1 in px ; GD2 in point
        $marge    = 10;   // between barcode and hri in pixel
        $x        = 80;  // barcode center
        $y        = 80;  // barcode center
        $height   = 50;   // barcode height in 1D ; module size in 2D
        $width    = 2;    // barcode height in 1D ; not use in 2D
        $angle    = 180;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation          
        $code     = $tracking; // barcode, of course ;)
        $type     = 'code39';
  
        $im     = imagecreatetruecolor(200, 150);
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);
        $red    = ImageColorAllocate($im,0xff,0x00,0x00);
        $blue   = ImageColorAllocate($im,0x00,0x00,0xff);
        imagefilledrectangle($im, 0, 0, 200, 150, $white);
        $data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
        $imageBarcode = imagegif($im,'./barcode.gif');
        
        $gdBarImage = imagecreatefromgif('./barcode.gif');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $barDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $barDrawing->setName('Sample image');
        $barDrawing->setDescription('Sample image');
        $barDrawing->setImageResource($gdBarImage);
        $barDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
        $barDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $barDrawing->setHeight(80);
        $barDrawing->setWorksheet($objPHPExcel->getActiveSheet());
        $barDrawing->setCoordinates('E7');
        
        
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Save Excel 2007 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        $objWriter->save('php://output');

        exit;
        
        
        
        
        
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
            );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}
