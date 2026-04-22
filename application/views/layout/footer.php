        </main><!-- /.admin-content -->
    </div><!-- /.admin-container -->
</div><!-- /.wrapper -->

<!-- Vendor JS — self-hosted to avoid CDN blocking on restricted networks -->
<script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/select2/js/select2.min.js'); ?>"></script>
<!-- Suspect custom JS -->
<script src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
