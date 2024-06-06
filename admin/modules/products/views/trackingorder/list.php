<div id="page-wrapper" class="page-wrapper-small" style="min-height: 537px;">
    <div class="form_head">
        <div id="wrap-toolbar" class="wrap-toolbar">
            <div class="fl">
                <h1 class="page-header">
                    Theo dõi hàng nhập kho
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
            <form class="form-horizontal" action="https://test.dienmayai.com/admin/products/trackingorder" name="adminForm" method="post">
                <div class="filter_area">
                    <div class="row">
                        <input type="hidden" name="text_count" value="2">			
                        <div class="fl-left pd-15">	
                            <input type="text" placeholder="Mã hàng" name="keysearch" id="search" value="<?= !empty($_SESSION[$this -> prefix.'keysearch'])?$_SESSION[$this -> prefix.'keysearch']:'' ?>" class="form-control fl-left">
                            
                            <span class="input-group-btn fl-left" style="margin-left: -2px;">
                            <!--<button onclick="this.form.submit();" class="btn btn-search btn-default" type="button">-->
                            <!--<i class="fa fa-search"></i>-->
                            <!--</button>-->
                            </span>
                        </div>
                        <div class="fl-left pd-15">
                            <input  type="date" placeholder="Ngày đặt hàng" name="order_date" id="order_date" value="" class="form-control fl-left">
                        </div>
                        
                        <div class="fl-left">				
                            <button class="btn btn-outline btn-primary" type="submit">Tìm kiếm</button>	
                            <button class="btn btn-outline btn-primary" onclick="document.getElementById('search').value='';				document.getElementById('text0').value=''; 				document.getElementById('text1').value=''; 				this.form.getElementById('filter_state').value='';this.form.submit();">Reset</button>
                        </div>
                        <input type="hidden" name="filter_count" value="4">			
                       
                       
                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <table style="width: 100%;" id="dataTables-example" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="title 1">Stt</th>
                                <th class="title 1">Mã nội bộ</th>
                                <th class="title 1">Ngày đặt hàng</th>
                                <th class="title 2" width="15%">Sku</th>
                                <th class="title 2">Mã nhanh</th>
                               
                                <th class="title 1">Tên sản phẩm </th>
                                <th class="title 1">Số lượng nhập</th>
                                <th class="title 1">Ngày phát hàng</th>
                                <th class="title 1">Ngày đến kho TQ </th>
                                <th class="title 1">Ngày Hoàn thành </th>
                                <th class="title 1">Ghi chú </th>
                               

                               
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            
                           
                            
                            if(!empty($result) && count($result)>0){
                            $dem = 0;
                            foreach($result as  $value){
                                $dem++;
                            ?>
                            <tr class="row0">
                                <td><?=$dem  ?></td>
                                <td><?=$value->internal_code??''  ?></td>
                                <td><?=$value->order_date??''  ?></td>
                                <td><?=$value->sku??''  ?></td>
                                <td><?=$value->fast_code??'' ?></td>
                                <td><?=$value->name??''  ?></td>
                                <td><?=$value->quantity??''  ?></td>
                                <td><?=$value->delivery_date??''  ?></td>
                                <td><?=$value->import_date??''  ?></td>
                                <td><?=$value->done_date??''  ?></td>
                                <td><?=$value->note??''  ?></td>
                               
                                
                               
                                
                            </tr>
                            
                            <?php
                                }}   
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
                    <input type="hidden" value="" name="sort_field"><input type="hidden" value="asc" name="sort_direct"><input type="hidden" value="products" name="module"><input type="hidden" value="trackingorder" name="view"><input type="hidden" value="51" name="total"><input type="hidden" value="0" name="page"><input type="hidden" value="" name="field_change"><input type="hidden" value="" name="task"><input type="hidden" value="0" name="boxchecked"><input type="hidden" value="0" name="page"><input type="hidden" value="0" name="limit">
                </div>
            </form>
        </div>
        <script>
            $(function() {
            	$( "#text0" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy'});
            	$( "#text1" ).datepicker({clickInput:true,dateFormat: 'dd-mm-yy'});
            });
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