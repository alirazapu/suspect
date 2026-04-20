        </main><!-- /.admin-content -->
    </div><!-- /.admin-container -->
</div><!-- /.wrapper -->

<!--
    jQuery + Bootstrap 4 JS
    For production: replace CDN links with locally vendored files:
      assets/vendor/jquery/jquery.min.js
      assets/vendor/bootstrap/js/bootstrap.bundle.min.js
-->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"
        integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
<!-- Suspect custom JS -->
<script src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
