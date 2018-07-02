<html><head>

<style>
/*    table, td, th, tr {
        border: 1px solid black;
        padding: 0px;
    }*/

    table {
        max-width: 600px;
        width: 100%;
        text-align: left;
        margin-top: 0px;
        margin-bottom: 0px;
        margin-left: auto;
        margin-right: auto;
    }

    h1 {
        font-family: arial;
        font-size: 36px;
        font-style: normal;
        font-weight: bold;
        text-align: left;
        text-decoration: none;
        color: #345E9E;
        display: inline;
    }

    h2 {
        font-family: arial;
        font-size: 28px;
        font-style: normal;
        font-weight: bold;
        text-align: left;
        text-decoration: none;
        color: #345E9E;
        display: inline;
    }

    span {
        font-family: arial;
        font-size: 16px;
        font-style: normal;
        text-align: left;
        color: #000000;
        text-decoration: none;
        font-weight: normal;
    }

    span.info {
        font-style: italic;
    }

    span.footer {
        font-size: 12px;
        word-wrap: break-word;
        padding-left: 10px;
        padding-right: 10px;
        color: #A1A1A1
    }

    table.social_footer {
        color: #606060; 
        font-family: Arial; 
        font-size: 11px; 
        text-align: center; 
        text-decoration: none;
    }

</style>

</head><body>
<title>You are a potential winner at Kizzang!</title>       

    <br>

    <!-- ** START HEADER ** -->
    <table style="text-align: center;">
        <tbody>
            <tr>
                <td >
                    <a href="https://<?php echo getenv("ENV") == "dev" ? "dev." : ""; ?>kizzang.com/ref?id=claim1&s=9&m=email&t=claim1&c=1&d=1" target="_blank"> 
                        <img src="https://d1w836my735uqw.cloudfront.net/header_images/potential_winner_header.jpg" style="max-width: 600px; width: 100%;" border="0" alt="">
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    <!-- ** END HEADER ** -->

    <!-- ** START BODY ** -->
    <table style="text-align: center;">
        <tbody>
            <tr>
                <td>        
                    <h1>
                        You Are a Potential Winner!
                    </h1>
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <table  style="background-color: #537BBE;">
        <tbody>
            <tr>
                <td>
                    <span style="font-size: 14px; color: white;">
                        Prize: <?= $prize; ?>
                        <br>
                        Game Name: <?= $game; ?>
                        <br>
                        Serial Number: <?= $serialNumber; ?>
                        <br>
                        Entry Number: <?= $winnerId; ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <table>
        <tbody>
            <tr>
                <td>
                    <span>
                        To start processing your prize, click the button below within <b>24 hours</b> to send a photo of your DMV-issued ID to Kizzang.
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    
    <table width="450px" align="center" valign="middle">
        <a href="https://<?php echo getenv("ENV") == "dev" ? "dev." : ""; ?>kizzang.com/ref?id=claim1&s=9&m=email&t=claim1&c=1&d=1" target="_blank"> 
          <div style="background-color: #61b606;
                          color: #ffffff;
                          display: block;
                          font-family: sans-serif;
                          font-size: 36px;
                          font-weight: bold;
                          text-align: center;
                          text-decoration: none;
                          width: 450px;
                          max-width: 450px;
                          min-width: 450px;
                          border-radius: 40px;
                          height: 75px;
                          line-height: 75px;">CLAIM MY PRIZE!
          </div>   
        </a>   
    </table>    

    <br>
    <br>

    <table>
        <tbody>
            <tr>
                <td>
                    <span>Our prize team will verify that your personal information is accurate and complete.  If you are eligible to receive this prize, <b>you will receive a phone call during regular business hours (9am - 6pm PST) within 48 hours.</b>
                        <br>
                        <br>
                        It may take up to 90 days to process your prize claim.
                        <br>
                        <br>
                        If you have any questions, please email <a href="mailto:winners@kizzang.com">winners@kizzang.com</a>.  We will be glad to assist you through this process.
                        <br>
                        <br>
                        Thank you for playing at Kizzang!  We hope to see you again soon.
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <!-- ** END BODY ** -->