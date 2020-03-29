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
  .h6 {padding: 0 0 15px 0; font-size: 12px; line-height: 28px; font-weight: bold; font color:white;}
  .bodycopy {font-size: 16px; line-height: 22px;}
  .button {text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;}
  .button a {color: #ffffff; text-decoration: none;}
  .button ab {text-align: center; font-size: 9px; font-family: sans-serif;color: #ffffff;}

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
              $costing_epm = 0;
              $costing_np_margin = 0;
              $smv = 0;
              $target_price = 0;
              $budget_epm = 0;
              $percentage_margin = 0;

              $breakdown = 0;
              $cost = 0;
              $cost_percentage = 0;

              $approver = "udara";
              $style_no  = QP1150;
              $merchandiser = "upul";
              $timestamp = "21st February 2020, 9:50 PM";
              $costing_stage = "sc";
              $customer_name = "sc";

              $1st_approve = "";
              $2nd_approve = "";
              $3rd_approve = "";

              $1st_approve_time = "";
              $2nd_approve_time = "";
              $3rd_approve_time = "";

             ?>

            <tr>
              <td class="h2"> <u>
                SMV Added to Style </u>
              </td>
            </tr>
            <tr>
              <td class="bodycopy">
                <p> Dear {{ $merchandiser }},
                  <br>
                  <br>
                  The costing submitted for Style No - {{ $style_no }} has been Approved/Rejected accordingly: <br>
                  <br>
                </p>
              <table class="table table-borderless" width="100%" border="0" cellspacing="0" cellpadding="0">

                <tr>
                  <th width="35%" align="left">1<sup>st</sup> approval by</th>
                  <th width="2%">:</th>
                  <td width="24%">{{$1st_approve}}</td>

                  <th width="25%" align="left">Date & Time</th>
                  <th width="2%">:</th>
                  <td width="15%">{{$1st_approve_time}}</td>
                </tr>
                <tr>
                  <th width="35%" align="left">2<sup>nd</sup> approval by</th>
                  <th width="2%">:</th>
                  <td width="24%">{{$2nd_approve}}</td>

                  <th width="25%" align="left">Date & Time</th>
                  <th width="2%">:</th>
                  <td width="24%">{{$2nd_approve_time}}</td>
                </tr>
                <tr>
                  <th width="35%" align="left">3<sup>rd</sup> approval by</th>
                  <th width="2%">:</th>
                  <td width="24%">{{$3rd_approve}}</td>

                  <th width="25%" align="left">Date & Time</th>
                  <th width="2%">:</th>
                  <td width="24%">{{$3rd_approve_time}}</td>

                </tr>

                </table>
                <br>
                <!-- <table class="table table-striped" width="100%" border="1">
        				  <thead>
        				    <tr>
        				      <th>Breakdown</th>
        				      <th>Cost ($)</th>
                      <th>Cost Percetage %</th>
                    </tr>
        				  </thead>
        				  <tbody>
        				  	@foreach($obj_details as $ratio)
        				    <tr>
        				      <td>{{ $breakdown }}</td>
                      <td>{{ $cost }}</td>
                      <td>{{ $cost_percentage }}</td>
              	    </tr>
        				    @endforeach
        				  </tbody>
        				</table> -->
                <!-- <p> Please reply with letter A to approve, R to reject, or approve on the system; </p> -->


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
                  <!-- <tr>
                    <td class="bodycopy">

                      This is an automatically generated email message from Surface&trade;.

                      </td>
                  </tr> -->
                  <tr>
                    <td style="padding: 10px 0 0 0;">
                      <table class="buttonwrapper" bgcolor="#e05443" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td class="button" height="45">
                            <a href="#">Click Here </a><br>
                            <ab>[ To View Costing  ]</ab>
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

      <!-- <tr>
        <td class="innerpadding borderbottom">
            <table class="table table-borderless" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td class="bodycopy">
                        <tr>
                          <th width="15%" align="left">1st approval by</th>
                          <th width="2%">:</th>
                          <td width="24%">{{$1st_approve}}</td>

                          <th width="15%" align="left">Date & Time</th>
                          <th width="2%">:</th>
                          <td width="15%">{{$1st_approve_time}}</td>
                        </tr>
                        <tr>
                          <th width="15%" align="left">2nd approval by</th>
                          <th width="2%">:</th>
                          <td width="24%">{{$2nd_approve}}</td>

                          <th width="15%" align="left">Date & Time</th>
                          <th width="2%">:</th>
                          <td width="24%">{{$2nd_approve_time}}</td>
                        </tr>
                        <tr>
                          <th width="15%" align="left">3rd approval by</th>
                          <th width="2%">:</th>
                          <td width="24%">{{$3rd_approve}}</td>

                          <th width="15%" align="left">Date & Time</th>
                          <th width="2%">:</th>
                          <td width="24%">{{$3rd_approve_time}}</td>

                        </tr>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
        </td>
      </tr> -->

       <tr>
        <td class="bodycopy">
            <center> <label style="font-size:10px;">This is an automatically generated email message from Surface&trade;.</label></center>
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
