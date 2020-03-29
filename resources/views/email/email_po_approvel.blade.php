<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>A Simple Responsive HTML Email</title>
  <style type="text/css">
  body {margin: 0; padding: 0; min-width: 100%!important;}
  img {height: auto;}
  .content {width: 100%; max-width: 600px;}
  .header {padding: 40px 30px 20px 30px;}
  .innerpadding {padding: 30px 30px 30px 30px;}
  .borderbottom {border-bottom: 1px solid #f2eeed;}
  .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
  .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
  .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
  .h2 {padding: 0 0 15px 0; font-size: 24px; line-height: 28px; font-weight: bold;}
  .bodycopy {font-size: 16px; line-height: 22px;}
  .button {text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;}
  .button a {color: #ffffff; text-decoration: none;}
  .footer {padding: 20px 30px 15px 30px;}
  .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
  .footercopy a {color: #ffffff; text-decoration: underline;}

  @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
  body[yahoo] .hide {display: none!important;}
  body[yahoo] .buttonwrapper {background-color: transparent!important;}
  body[yahoo] .button {padding: 0px!important;}
  body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important;}
  body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
  }

  /*@media only screen and (min-device-width: 601px) {
    .content {width: 600px !important;}
    .col425 {width: 425px!important;}
    .col380 {width: 380px!important;}
    }*/

  </style>
</head>

<body yahoo bgcolor="#E2E2E2">
<table width="100%" bgcolor="#E2E2E2" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td>
    <!--[if (gte mso 9)|(IE)]>
      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
    <![endif]-->
    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td bgcolor="#37474f" class="header">
          <table width="70" align="left" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" >
                <img class="fix" src="http://test-surface.helaclothing.com/test/surfacedev/resources/images/logo_light.png" width="150" border="0" alt="" /><br>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
            <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
          <![endif]-->
          <!--<table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;">
            <tr>
              <td height="70">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="subhead" style="padding: 0 0 0 3px;">
                      CREATING
                    </td>
                  </tr>
                  <tr>
                    <td class="h1" style="padding: 5px 0 0 0;">
                      Responsive
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>-->
          <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
          </table>
          <![endif]-->
        </td>
      </tr>
      <tr>
        <td class="innerpadding borderbottom">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">

            <?php
              $obj = json_decode($po->po_header);
              $obj_details = json_decode($po->po_details);
              $po_number = $obj->po_number;
              $po_type = $obj->po_type;
              $po_date = explode("T",$obj->po_date);
              $supplier = $obj->supplier;
              $deli_date = explode("T",$obj->delivery_date);
              $deliverto = $obj->deliverto->loc_name;
              $invoiceto = $obj->invoiceto->company_name;
             ?>

            <tr>
              <td class="h2">
                APPROVAL PENDING PO - {{ $po_number }}
              </td>
            </tr>
            <tr>
              <td class="bodycopy">
                <table class="table table-borderless" width="100%" border="0" cellspacing="0" cellpadding="0">

      					  <tr>
      					    <th width="24%" align="left">PO TYPE</th>
      					    <th width="2%">:</th>
      					    <td width="24%">{{$po_type}}</td>
      					    <th width="24%" align="left">PO DATE</th>
      					    <th width="2%">:</th>
      					    <td width="24%">{{$po_date[0]}}</td>
      					  </tr>

                  <tr>
      					    <th width="24%" align="left">SUPPLIER NAME</th>
      					    <th width="2%">:</th>
      					    <td width="24%">{{$supplier}}</td>
      					    <th width="24%" align="left">DELIVERY DATE</th>
      					    <th width="2%">:</th>
      					    <td width="24%">{{$deli_date[0]}}</td>
      					  </tr>

                  <tr>
      					    <th width="24%" align="left">DELIVERY TO</th>
      					    <th width="2%">:</th>
      					    <td width="24%" colspan="4">{{ $deliverto }}</td>

      					  </tr>

                  <tr>
      					    <th width="24%" align="left">INVOICE TO</th>
      					    <th width="2%">:</th>
      					    <td width="24%" colspan="4">{{ $invoiceto }}</td>

      					  </tr>

                </table>

                <br>

                <table class="table table-striped" width="100%" border="1">
        				  <thead>
        				    <tr>
        				      <th>Meterial</th>
        				      <th>Item Description</th>
                      <th>Price</th>
                      <th>Qty</th>
                      <th>Value</th>
        				    </tr>
        				  </thead>
        				  <tbody>
        				  	@foreach($obj_details as $ratio)
        				    <tr>
        				      <td>{{ $ratio->category_name }}</td>
                      <td>{{ $ratio->master_description }}</td>
                      <td>{{ $ratio->unit_price }}</td>
                      <td>{{ $ratio->tra_qty }}</td>
                      <td>{{ $ratio->value_sum }}</td>

        				    </tr>
        				    @endforeach
        				  </tbody>
        				</table>








              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="innerpadding borderbottom">

          <table class="col380" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 380px;">
            <tr>
              <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="bodycopy">

                      This is an automatically generated email message from SurfaceTM.

                      </td>
                  </tr>
                  <tr>
                    <td style="padding: 10px 0 0 0;">
                      <table class="buttonwrapper" bgcolor="#e05443" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td class="button" height="45">
                            <a href="#">Login To System</a>
                          </td>
                        </tr>
                      </table>
                    </td>

                  </tr>
                </table>
              </td>
            </tr>
          </table>

        </td>
      </tr>

      <tr>
        <td class="footer" bgcolor="#37474f">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" class="footercopy">
                &reg; HELA CLOTHING PVT LTD, 2019<br/>

              </td>
            </tr>
            <tr>
              <td align="center" style="padding: 20px 0 0 0;">
                <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <!--[if (gte mso 9)|(IE)]>
          </td>
        </tr>
    </table>
    <![endif]-->
    </td>
  </tr>
</table>
</body>
</html>
