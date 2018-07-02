<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Final 3 Entry - Kizzang</title>
</head>
<body>
  <table width="700" border="0" cellspacing="0" cellpadding="0" class="tableContent bgBody" align="center"  style='border: #000000 solid 4px;width: 100%; max-width: 700px; font-family:Helvetica, sans-serif;
background: #e7e6e9; 
background: -moz-linear-gradient(top,  #e7e6e9 0%, #889099 48%, #889099 53%, #4e227b 100%); 
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e7e6e9), color-stop(48%,#889099), color-stop(53%,#889099), color-stop(100%,#4e227b)); 
background: -webkit-linear-gradient(top,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
background: -o-linear-gradient(top,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
background: -ms-linear-gradient(top,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
background: linear-gradient(to bottom,  #e7e6e9 0%,#889099 48%,#889099 53%,#4e227b 100%); 
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e7e6e9', endColorstr='#4e227b',GradientType=0 );'>
    <!-- =============== START HEADER =============== -->
    
  <tr>
    <td align='center' class="bgItem">
      <table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="width: 100%; max-width: 600;">
        
 <!-- =============== END HEADER =============== -->
          <!-- =============== START BODY =============== -->

        <tr>
          <td align='center' class='movableContentContainer'>
              <table width="580" border="0" cellspacing="0" cellpadding="0" align="center" style="width: 100%; max-width: 580;">
                <tr><td height='10' colspan="3"></td></tr>
                <tr>
                  <td>
<!-- ========== KIZZANG LOGO ========== -->
<center><img src="https://s3.amazonaws.com/kizzang-campaigns/emails/kz_logo.png" alt='Kizzang' width='150'/></center>
                  </td>
                </tr>
                <tr><td height='5' colspan="3"></td></tr>
              </table>

            <table width="525" border="0" cellspacing="0" cellpadding="0" align="center" style="background-color: #ffffff;max-width: 525px;min-width: 295px;width: 80%;margin: 10px auto 5px;box-shadow: 0px 0px 20px #000;min-height: 800px;display: block;border-radius: 5px;border: 2px solid #a0abb7;">
              <tr>
                <td bgcolor="#fff">
                  <table width="580" border="0" cellspacing="0" cellpadding="0" align="center" style="width: 100%; max-width: 580;">
                      <tr>
                        <td align="center">
                          <img src="https://d1w836my735uqw.cloudfront.net/logos/final_3_logo_for_email.png" alt='header' data-default="placeholder" width='280' height='280' style="margin-left: auto; margin-right: auto; text-align: center; padding-top: 10px;">
                        </td>
                      </tr>
                      <tr><td height='5'></td></tr>
                <?php foreach($cards as $card) : ?>
                <?php foreach($card->answers as $answer) : ?>
                 <tr align="center">
                        <td align="center"  width="500" style="width: 100%; max-width: 580; margin-left: auto; margin-right: auto; text-align: center;">
                          <h3 style="text-align: center;font-size:22px;margin-bottom:5px; color: #16328f;"><?= $answer['title']; ?></h3>
                          <h4 style="text-align: center;margin: 5px 0;
                                font-size: 18px;"><?= $answer['game_time']; ?></h4>
                          </td>
                      </tr>
                      <tr>
                        <td align='center'>
                          <table width="300" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-bottom: -20px;">
                            <tr>
                              <td class="col" width="150" valign="top">
                                <table cellpadding="0" cellspacing="0" align="center">
                                  <tr>
                                    <td class="headline" align="left" style="font-family: arial,sans-serif; font-size: 18px; color: #96191b; padding-top: 0px;">
                                      <b><?= $answer['team1']; ?></b>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="left" style="font-family: arial,sans-serif; font-size: 16px; color: #333; text-align: center;">
                                      <b><?= $answer['team1score']; ?></b>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                              <td class="col" width="150" valign="top">
                                <table cellpadding="0" cellspacing="0" align="center">
                                  <tr>
                                    <td class="headline" align="left" style="font-family: arial,sans-serif; font-size: 18px; color: #96191b; padding-top: 0px;">
                                       <b><?= $answer['team2']; ?></b>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="body_copy" align="left" align="left" style="font-family: arial,sans-serif; font-size: 16px; color: #333; text-align: center;">
                                    <b><?= $answer['team2score']; ?></b>
                                    </td>
                                  </tr>                                  
                                </table>
                              </td>
                            </tr>                            
                          </table>
                        </td>
                      </tr>
                <?php endforeach; ?>
                      <tr><td height='50'></td></tr>
               <?php endforeach; ?>
<!-- ============ SPACER ============== -->

<!-- ============ FOOTER PRIVACY TEXT ============== -->

            <tr style="margin-top: 20px; padding-top: 20px; padding-left: 20px; padding-right: 20px;">
                <td align="center" style="text-align: center;">
                    <a href="https://<?php echo getenv("ENV") == "dev" ? "dev." : ""; ?>kizzang.com/ref?id=wel1&s=9&m=email&t=wel1&c=1&d=1" style="text-decoration: none;">
<!-- ============ KIZZANG LOGO ============== -->
                        <img src="https://d1zi7avb7sonrn.cloudfront.net/13.png" width="215px" style="width: 100%; max-width: 215px; text-align: left; margin-bottom: -5px;">
                    </a><br> 
                    <span style="font-size: 10px; word-wrap: break-word; padding-left: 30px; padding-right: 30px; color: #A1A1A1"><br>
                        <!-- ** PRIVACY TEXT ** -->

                        &#169; 2016 Kizzang&#174;. All rights reserved.<br>
                        <b>NO PURCHASE NECESSARY.</b><br>
                         Must be a legal resident of the United States, residing in one of the 50 states or District of Columbia. Must be 21 years of age or older to participate.
                    </span>
                    <br><br>
                    <span style="font-size: 12px; word-wrap: break-word; padding-left: 10px; padding-right: 10px; color: #A1A1A1">                       
                        <!-- ** OPT OUT KEY ** -->
                        Don't want to receive these promotional reminder emails? 
                        <a href="https://<?php echo getenv("ENV") == "dev" ? "dev." : ""; ?>kizzang.com/accounts/optout/[OPTOUT_KEY]" style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-weight: normal;text-decoration: underline;">Click here to unsubscribe.</a>
                    </span>
                    <br><br>
                    <span class="padbot" style=" margin: 0; font-size: 10px; text-align: center; color: #A1A1A1;">
                        <!-- ** CONTACT INFO ** -->
                        Questions or Comments? Email us at CustomerService@Kizzang.com.<br>
                       Kizzang LLC <br> P.O. Box 82160, Las Vegas, NV 89180<br><br>
                </span>
              </span>
            </td>
            </tr>


                      <tr><td height='20'></td></tr>
                    </table>
                  </td>
                </tr>
              </table>

              <table>
              <tr><td height='35' colspan="3"></td></tr>
              </table>
            </div>


          </td>
        </tr>
  
<!-- =============== END FOOTER =============== -->
      </table>
    </td>
  </tr>
</table>
</body>
  </html>