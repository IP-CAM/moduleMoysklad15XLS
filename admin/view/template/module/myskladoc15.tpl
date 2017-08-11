<?php echo $header; ?>
<?php 
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

$token = $_GET['token'];

?>
<link href="view/stylesheet/mysklad.css" rel="stylesheet">

<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ajaxupload.3.5.js"></script>

<div id="content" style="margin-left:50px;">
 

<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1 style="float:left;"><img src="view/image/module.png" alt="" /><?php echo $heading_title; ?></h1>
      <div class="buttons" style="float: right;"><a onclick="$('#form-category').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
      </div>

  <div class="content">
    <div class="panel panel-default">
      <div class="panel-body">
        <div id="tabs" class="htabs">
          <a href="#tab-general"><?php echo $text_tab_general; ?></a>
          <a href="#tab-product"><?php echo $text_tab_product; ?></a>
          <a href="#tab-order"><?php echo $text_tab_order; ?></a>
          <a href="#tab-author"><?php echo $text_tab_author; ?></a>
        </div>
        <!--
        Начало формы
          !-->
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-category" class="form-horizontal">

          <div id="tab-general">
            <table class="form">
              <tr>
                <td><?php echo $entry_username; ?></td>
                <td><input name="myskladoc15_username" type="text" value="<?php echo $myskladoc15_username; ?>" /></td>
              </tr>
              <tr>
                <td><?php echo $entry_password; ?></td>
                <td><input name="myskladoc15_password" type="password" value="<?php echo $myskladoc15_password; ?>" /></td>
              </tr>

              <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="myskladoc15_status">
                    <?php if ($myskladoc15_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>

            </table>
          </div>

          <div id="tab-product">
            <table class="form">
              <tr>
                <td>
                  <?php echo $entry_upload; ?>
                </td>
                <td>
                  <a id="button-upload" class="button"><?php echo $button_upload; ?></a>
                  <div class="message">
                    <span id="status" ></span>
                    <ul id="files" ></ul>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo $entry_download; ?>
                  <div class="diapason">
                    <input type="text" name="ot" class="ot" value="0"> -
                    <input type="text" name="do" class="kolichestvo" value="1000">
                  </div>
                </td>
                <td>
                  <a id="button-downoload" class="button"><?php echo $button_download; ?></a>
                  <div class="diapason_text">
                    <p>
                      <?=$diapason_text;?>
                    </p>
                  </div>
                </td>

              </tr>
            </table>
          </div>

          <div id="tab-order">
            <table class="form">

              <tr>
                <td><?php echo $entry_order_status_to_exchange; ?></td>
                <td>
                  <select name="myskladoc15_order_status_to_exchange">
                    <option value="0" <?php echo ($myskladoc15_order_status_to_exchange == 0)? 'selected' : '' ;?>><?php echo $entry_order_status_to_exchange_not; ?></option>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <option value="<?php echo $order_status['order_status_id'];?>" <?php echo ($myskladoc15_order_status_to_exchange == $order_status['order_status_id'])? 'selected' : '' ;?>><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>

              <tr>
                <td><?php echo $entry_order_status; ?></td>
                <td>
                  <select name="myskladoc15_order_status">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <option value="<?php echo $order_status['order_status_id'];?>" <?php echo ($myskladoc15_order_status == $order_status['order_status_id'])? 'selected' : '' ;?>><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>

   </table>
          </div>
          <div id="tab-author">
             <div class="author">
               <p>Created by: <a href="http://isyms.ru/">Artur Legusha</a></p>
             </div>
            </table>
          </div>
 </form>

        <!--
        Конец формы
          !-->
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript"><!--
  $('#tabs a').tabs();
  //--></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#button-downoload').click(function(){
      var ot_diapason = $('.ot').val();
      var kolichestvo_diapason = $('.kolichestvo').val();

       if (kolichestvo_diapason > 1001){
        alert ("Error");
      }else{
        $.ajax({
          url : 'index.php?route=module/myskladoc15/download&token=<?php echo $token;?>',
          type : 'post',
          dataType:'text',
          data :{
            ot: ot_diapason,
            kolichestvo: kolichestvo_diapason
          },
          success:function(data){
            location.href = data;
 
          },

        });
      }


    });

    //import var good;  importxls ()

  });

  $(function(){
    var btnUpload=$('#button-upload');
    var status=$('#status');
    new AjaxUpload(btnUpload, {

      action: 'upload-file.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){
        if (! (ext && /^(xls|xlsx)$/.test(ext))){
          // extension is not allowed
          status.text('Поддерживаемые форматы xls, xlsx');
          return false;
        }
        status.text('Загрузка...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        //Add uploaded file to list
         if(response==="success"){
          $('<li></li>').appendTo('#files').html('<img src="view/image/ok.png" alt="" /><br />'+file).addClass('success');
           $.ajax({
             url : 'index.php?route=module/myskladoc15/importxls&token=<?php echo $token;?>',
             type : 'post',
             dataType:'text',
             data :{
               good: "good",

             },
             success:function(data){
              console.log(data);

             },

           });
        } else{
          $('<li></li>').appendTo('#files').text('Файл не загружен' + file).addClass('error');

          console.log(response);
        }
      }
    });

  });


</script>



<?php echo $footer; ?>