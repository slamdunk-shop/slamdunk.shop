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
<?endif;?>