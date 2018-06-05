<?php if (!isset($_COOKIE['cookiePolicy']) && $_COOKIE['cookiePolicy'] != true): ?>
    <div class="footer-cookie">
        <div class="footer-cookie--message">
            На сайте slamdunk.shop используются cookie-файлы и другие аналогичные технологии. Если, прочитав это сообщение, вы остаетесь на нашем сайте, это означает, что вы не возражаете против использования этих технологий и <a href="/privacy-policy/">обработки персональных данных</a>.
        </div>
        <button class="footer-cookie--button">
            OK
        </button>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery('.footer-cookie--button').on('click', function () {
                Cookies.set('cookiePolicy', true);
                jQuery(this).parent('.footer-cookie').hide();
            });
        });
    </script>
    <style>
        .footer-cookie {
            position: fixed;
            width: 386px;
            min-height: 283px;
            left: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            padding: 25px;
        }
        .footer-cookie--message {
            position: relative;
            display: inline-block;
            font-family: PT Sans;
            font-style: normal;
            font-weight: normal;
            line-height: normal;
            font-size: 18px;
            color: #FFFFFF;

        }
        .footer-cookie--message a {
            color: #FFFFFF;
            text-decoration: underline;
        }
        .footer-cookie--button {
            display: inline-block;
            margin-top: 25px;
            width: 100%;
            height: 47px;
            background: #FC6000;
            font-family: PT Sans;
            font-style: normal;
            font-weight: bold;
            line-height: normal;
            font-size: 18px;
            text-align: center;
            color: #FFFFFF;
        }

        @media (max-width: 425px) {
            .footer-cookie {
                width: 100%;
            }
            .footer-cookie--button {
                width: 166px;
                display: block;
                margin: 25px auto 0;
            }
        }
    </style>
<?endif;?>