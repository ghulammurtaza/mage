<?php

class TM_CheckoutFields_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    /**
     * Draw additional checkout fields
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawCheckoutFields(Zend_Pdf_Page $page, $order)
    {
        if ($order instanceof Mage_Sales_Model_Order_Shipment) {
            $order = $order->getOrder();
        }

        $fields = Mage::helper('checkoutfields')->getFields();
        $lines  = array();
        $i      = 0;
        foreach ($fields as $field => $config) {
            $value = (string)$order->getData($field);

            if (!strlen($value)) {
                continue;
            }

            $lines[$i][] = array(
                'text' => $config['label'],
                'feed' => 35
            );
            $lines[$i][] = array(
                'text' => $value,
                'feed' => 200
            );

            $i++;
        }

        if ($lines) {
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
            $this->y -= 5;
            $lineBlock = array(
                'lines'  => $lines,
                'height' => 14
            );

            $this->drawLineBlocks($page, array($lineBlock));
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        }
    }

    /**
     * Overriden to add call for _drawCheckoutFields method
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        /*
        foreach ($invoices as $invoice) {
             //echo "<pre>"; print_r($invoice);
             echo "new <br><br>";
             $order = $invoice->getOrder();
             $shipping_address = $order->getShippingAddress();
             $fields = Mage::helper('checkoutfields')->getFields();
             foreach ($fields as $field => $config){
                 echo $value = (string)$order->getData($field);
                 echo "<br />";
                 echo $field.'<br />';
                 if (!strlen($value)) :
                    continue;
                endif;
             }
             //echo "<pre>"; print_r($fields);
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
             //echo $invoice->getBaseShippingAmount();
             //print_r($address);
             foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                //echo $item->getOrderItemId();
                //echo $item->getBasePrice();
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
        header("Content-Disposition: attachment;filename=Invoice$catenate.xlsx"); // à¹à¸¥à¹‰à¸§à¸™à¸µà¹ˆà¸à¹‡à¸Šà¸·à¹ˆà¸­à¹„à¸Ÿà¸¥à¹Œ
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
        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('A6', 'Floor #');
        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Appartment #');
        $objPHPExcel->getActiveSheet()->setCellValue('A8', 'Building #');
        $objPHPExcel->getActiveSheet()->setCellValue('A9', 'Fax');
        $objPHPExcel->getActiveSheet()->setCellValue('A10', 'Address');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D3:E3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:C5');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:C10');
        $objPHPExcel->getActiveSheet()->setCellValue('A11', 'Telephone');
        $objPHPExcel->getActiveSheet()->setCellValue('A14', 'QUANTITY');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B14:C14');
        $objPHPExcel->getActiveSheet()->setCellValue('B14', 'DESCRIPTION');
        $objPHPExcel->getActiveSheet()->setCellValue('D14', 'AMOUNT');
        $objPHPExcel->getActiveSheet()->setCellValue('E14', 'TOTAL AMOUNT');

        foreach ($invoices as $invoice) {
             $order = $invoice->getOrder();
             $invoiceNo = $invoice->getIncrementId();
             $shipping_address = $order->getShippingAddress();
             $name = $shipping_address->getFirstname().' '.$shipping_address->getLastname();
             $telephone = $shipping_address->getTelephone();
             $address =  $shipping_address->getStreet();
             $shippingAddress = $address[0].' '.$shipping_address->getCity().', '.$shipping_address->getRegion().', '.$shipping_address->getPostcode();
             $total = $invoice->getBaseGrandTotal();
             /*
             $fields = Mage::helper('checkoutfields')->getFields();
             foreach ($fields as $field => $config){
                 $value = (string)$order->getData($field);
                 if($field =='tm_field2'){   // Floor #
                    $objPHPExcel->getActiveSheet()->setCellValue('B8', $value);    
                 }
                 elseif($field =='tm_field1'){      //Appartment #
                    $objPHPExcel->getActiveSheet()->setCellValue('B9', $value);     
                 }
                 if (!strlen($value)) :
                    continue;
                endif;
             }
             */
             $appartment_number = $shipping_address->getAppartmentNumber();
             $floor_number = $shipping_address->getFloorNumber();
             $building_number = $shipping_address->getBuildingNumber();
             $fax = $shipping_address->getFax(); 
             $objPHPExcel->getActiveSheet()->setCellValue('B6', $floor_number);
             $objPHPExcel->getActiveSheet()->setCellValue('B7', $appartment_number);
             $objPHPExcel->getActiveSheet()->setCellValue('B8', $building_number);
             $objPHPExcel->getActiveSheet()->setCellValue('B9', $fax);
             $objPHPExcel->getActiveSheet()->setCellValue('B1', $invoiceNo);
             $objPHPExcel->getActiveSheet()->setCellValue('B5', $name);
             $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:C10');
             $objPHPExcel->getActiveSheet()->setCellValue('B10', $shippingAddress);
             $objPHPExcel->getActiveSheet()->setCellValue('B11', $telephone);
             $objPHPExcel->getActiveSheet()->setCellValue('D3', $total.' EGP');
             $i = 15;
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
            $objPHPExcel->getActiveSheet()->setCellValue('E22', $shippingAmount);
            $objPHPExcel->getActiveSheet()->setCellValue('E24', $total);
        }
        $today = date("j/n/Y");
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B22:C22');
        $objPHPExcel->getActiveSheet()->setCellValue('B22', 'Aramex Shipping Fee');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B23:C23');
        $objPHPExcel->getActiveSheet()->setCellValue('B23', 'Cash on Delivery Fee (if applicable)');
        $objPHPExcel->getActiveSheet()->setCellValue('E23', 0.00);
        $objPHPExcel->getActiveSheet()->setCellValue('B24', 'Books.com.eg, Inc.');
        $objPHPExcel->getActiveSheet()->setCellValue('D24', 'TOTAL: EGP');
        $objPHPExcel->getActiveSheet()->setCellValue('B25', 'support@books.com.eg');
        $objPHPExcel->getActiveSheet()->setCellValue('D25', 'Date:');
        $objPHPExcel->getActiveSheet()->setCellValue('E25', $today);

        
        //Cell Styling --------------------------------------------------------------------
        // Add Font bold and border
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D3:E3')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A10')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A11')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A14')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B14:C14')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D14')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E14')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B22:C22')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B23:C23')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D24:E24')->applyFromArray($styleArray);
        
        $counter = 6;
        for($k=1; $k<$counter; $k++){
            if($k ==27)
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
        $objPHPExcel->getActiveSheet()->getStyle('A14')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('B14')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D14')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('E14')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('A22:A23')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('B22')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('E22')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('B23')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D22')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D23')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('E23')->applyFromArray($stylebgColor);
        $objPHPExcel->getActiveSheet()->getStyle('D24:E24')->applyFromArray($stylebgColor);

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
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            );

            // override start
            $this->_drawCheckoutFields($page, $order);
            // override end

            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }
}
