<style>
   .dataTable_wrapper{
       height: 500px;
       overflow-y: auto;
   } 
</style>


<div id="page-wrapper" class="page-wrapper-small" style="min-height: 537px;">
    <div class="form_head">
        <div id="wrap-toolbar" class="wrap-toolbar">
            <div class="fl">
                <h1 class="page-header">
                    Lợi nhuận gian hàng 
                    
                    
                </h1>
                <!--end: .page-header -->
                <!-- /.row -->    
            </div>
           
            <div class="clearfix"></div>
        </div>
        <!--end: .wrap-toolbar-->
    </div>
    <!--end: .form_head-->
    <div class="panel panel-default">
        <div class="panel-body">
                <div class="">
                    <div class="show-info">
                       
                    </div>    
                </div>    
                <div class="dataTable_wrapper">
                    <table style="width: 100%;" id="dataTables-example" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>stt</th>
                                <th class="title 1">Ngày chứng từ </th>
                                <th class="title 1">Số chứng từ </th>
                                <th class="title 1">Số hóa đơn</th>
                                <th class="title 2" width="15%">Diễn giải chung</th>
                                <th class="title 2">Diễn giải</th>
                               
                                <th class="title 1">Khách hàng </th>
                                <th class="title 1">Tên khách hàng</th>
                                <th class="title 1">Địa chỉ</th>
                                <th class="title 1">Mã hàng </th>
                                <th class="title 1">Tên hàng </th>
                                <th class="title 1">ĐVT </th>
                                <th class="title 1">Số lượng </th>
                                <th class="title 1">Đơn giá </th>
                                <th class="title 1">Tk Nợ  </th>
                                <th class="title 1">Tk Có </th>

                                <th class="title 2">Doanh số bán </th>
                                <th class="title 2">Tổng số lượng trả </th>
                                <th class="title 2">Giá trị trả lại </th>
                                <th class="title 2">Số phiếu nhập xuất </th>
                                <th class="title 2">Tk giá vốn </th>

                                <th class="title 2">TK kho </th>
                                <th class="title 2">Mã kho </th>
                                <th class="title 2">Tên kho </th>
                                
                                <th class="title 2">Giá bán thấp nhất  </th>
                                
                                <th class="title 2">Mã hàng lazada </th>
                                <th class="title 2">CPĐG </th>
                                <th class="title 2">CPĐG SAU COMBO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalrevenue  = $total_return_value =  $totalnet_revenue = $add_small_price =  $totalcost = $totalreturn_cost = $totalpackaging_cost = $totalshipping_cost = $totalads_cost = $totalother_cost = $totalgross_profit = $total_price_sale = $total_price_return = $total_cpdg_sauco = 0;
                          
                            if(!empty($result) && count($result)>0){
                            $dem = 0;
                            
                
                            foreach($result as  $value){
                                $dem++;
                                
                            ?>
                            
                            <?php 
                                    
                                $small_price =  ($model->showPriceSmall($value->sku))->price;
                                
                                $add_small_price += ($small_price*intval($value->quantity));
                                
                                $total_return_value += ($small_price*intval($value->return_revenue));
                                
                                $total_price_sale += (intval($value->price)*intval($value->quantity));
                                
                                $total_price_return += (intval($value->price)*intval($value->return_revenue));
                                
                                $total_cpdg_sauco += intval($value->cpdg_sauco);
                                
                                
                            ?>
                            
                            
                            <tr class="row0">
                                <td><?=$dem  ?></td>
                                <td><?=  date("d-m-Y", strtotime($value->date_document))  ?></td>
                                <td><?=$value->number_document  ?></td>
                                <td><?=$value->invoice_number  ?></td>
                                <td><?=$value->broad_interpretation ?></td>
                                <td><?=$value->explain  ?></td>
                                <td><?=$value->client_id   ?></td>
                                <td><?= $value->name_client ?></td>
                                <td><?=$value->address  ?></td>
                                <td><?=$value->sku  ?></td>
                                <td><?=$value->product_name  ?></td>
                                <td><?=$value->unit  ?></td>
                                <td><?=$value->quantity ?></td>
                                <td><?= str_replace(',', '.', number_format(intval($value->price), 0))  ?></td>
                                <td style="font-weight:bold"><?=$value->tk_no  ?></td>
                                <td style="font-weight:bold"><?=$value->tk_co  ?></td>
                                <td><?=  str_replace(',', '.', number_format(intval($value->revenue), 0))   ?></td>
                                <td><?=$value->return_revenue  ?></td> 
                                <td><?= str_replace(',', '.', number_format(intval($value->return_value), 0))     ?></td>
                                <td><?=$value->number_of_entries  ?></td>
                                <td style="font-weight:bold"><?=$value->tk_gia_von  ?></td>
                                 <td style="font-weight:bold"><?=$value->tk_kho  ?></td>
                                <td><?=$value->ma_kho  ?></td>
                                <td><?=$value->ten_kho  ?></td>
                                <td> <?= str_replace(',', '.', number_format(intval($small_price), 0)) ?> </td>
                                <td><?=$value->lazada_code  ?></td>
                                <td><?= str_replace(',', '.', number_format(intval($value->cpdg), 0))     ?> </td>
                                <td><?= str_replace(',', '.', number_format(intval($value->cpdg_sauco), 0))     ?></td>
                               
                               
                            <?php     
                                }
                                
                                ?>
                                
                               
                                
                                
                            </tr>
                            
                            <?php
                                }   
                            ?>
                          
                        </tbody>
                    </table>
                    <!--<nav aria-label="Page navigation">-->
                    <!--    <div style="text-align: center; font-weight: bold; margin-top: 30px;"><font>Tổng</font> : <span style="color:red">[120838]</span> </div>-->
                    <!--    <ul class="pagination">-->
                    <!--        <li><a class="title_pagination">Trang</a></li>-->
                    <!--        <li><a title="Page 1" class="current">[1]</a></li>-->
                    <!--        <li><a title="Page 2" href="https://test.dienmayai.com/admin/order/upload?page=2">2</a></li>-->
                    <!--        <li><a title="Page 3" href="https://test.dienmayai.com/admin/order/upload?page=3">3</a></li>-->
                    <!--        <li><a title="Page 4" href="https://test.dienmayai.com/admin/order/upload?page=4">4</a></li>-->
                    <!--        <li><a title="Page 5" href="https://test.dienmayai.com/admin/order/upload?page=5">5</a></li>-->
                    <!--        <li><a title="Page 6" href="https://test.dienmayai.com/admin/order/upload?page=6">6</a></li>-->
                    <!--        <b>..</b> -->
                    <!--        <li><a aria-label="Previous" title="Next page" href="https://test.dienmayai.com/admin/order/upload?page=2">›</a></li>-->
                    <!--        <li><a aria-label="Next" title="Last page" href="https://test.dienmayai.com/admin/order/upload?page=2417">»</a></li>-->
                    <!--    </ul>-->
                    <!--</nav>-->
                   
                </div>
                
                <?php 
                    $DSB = intval($add_small_price);
                    $DSTL = intval($total_return_value);
                    $GVHB = intval($total_price_sale);
                    $GVHTL = intval($total_price_return);
                    $LNT = intval($add_small_price) - intval($total_return_value);
                    $LNTT = $LNT -($GVHB -$GVHTL) - $total_cpdg_sauco;
                    $CPK  =  $LNT - $GVHB + $GVHTL -$LNTT;
                
                ?>
                
                <div class="info-item-show">
                    
                    <table style="width: 100%;" id="dataTables-example" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Doanh số bán</th>
                                <th>Doanh số trả lại</th>
                                <th>Lợi nhuận thuần</th>
                                
                                <th>Giá vốn hàng bán</th>
                                <th>Giá vốn hàng trả lại</th>
                               <th>Tổng chi phí khác (đóng gói, vận chuyển, QC,...)</th>
                                <th>Lợi nhuận tạm tính</th>
                                
                            </tr>
                            
                            <tr>
                                <th><?= str_replace(',', '.', number_format(intval($add_small_price), 0))  ?></th>
                                <th><?= str_replace(',', '.', number_format(intval($total_return_value), 0))  ?></th>
                                 <th><?=  str_replace(',', '.', number_format(intval($add_small_price) - intval($total_return_value), 0))  ?></th>
                                <th><?=  str_replace(',', '.', number_format(intval($total_price_sale), 0))  ?></th>
                                <th><?=  str_replace(',', '.', number_format(intval($total_price_return), 0))  ?></th>
                                 <th style="width:15%"><?=  str_replace(',', '.', number_format(intval($CPK), 0))  ?></th>
                                <th><?=  str_replace(',', '.', number_format(intval($LNTT), 0))  ?></th>
                               
                            </tr>
                            
                            
                        </thead>
                    </table>     
                    
                </div>
                
            
        </div>
        <script>
            $(function() {
            	$( "#text0" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy'});
            	$( "#text1" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy'});
            });
            
            $('.info-item-show').hide();
            
            var infoShow = $('.info-item-show').html();
            
        
            
            $('.show-info').append(infoShow);
        </script>
        <style type="text/css">
            .note-top{
            color: red;
            font-size: 16px;
            margin-bottom: 15px;
            text-align: center;
            }
        </style>
    </div>
    <!-- /#page-wrapper -->
</div>