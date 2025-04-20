<footer class="main-footer">
    <div class="badge_env_infos">V <?= APP_VERSION ?> - <?php echo displayEnvMessage() ?></div>
    <p>&copy; <a title="Visit my website" href="https://alexander.aulong.fr" target="_blank">Alexander AULONG</a> - <?php echo date("Y") ?></p>
    <div id="footer-links">
        <a title="Contact me" href="<?= $base_url ?>contact.php">contact</a>
        - <a title="About this app" href="<?= $base_url ?>about.php">about</a>
        - <a title="Legal" href="#">legal</a>
    </div>

</footer>
</body>
</html>