</div>
<?php include('../partials/footer.php') ?>
</div>
<?php include('../partials/quickView.php') ?>
</div>
</div>
<script src="http://localhost/fyp_project/public/assets/js/pages/dashboard-default.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="http://localhost/fyp_project/public/assets/js/vendors.min.js"></script>
<script src="http://localhost/fyp_project/public/assets/vendors/chartjs/Chart.min.js"></script>
<script src="http://localhost/fyp_project/public/assets/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="http://localhost/fyp_project/public/assets/vendors/datatables/dataTables.bootstrap.min.js"></script>
<script src="http://localhost/fyp_project/public/assets/js/app.min.js"></script>
<script>
    $(document).ready(function() {
        $('#data-table').DataTable();
    });
</script>
<script>
    var deleteFormId = '';

    function openDeleteModal(event, name, id) {
        event.stopPropagation();
        deleteFormId = id;
        var modalBody = document.getElementById('modalBodyContent');
        modalBody.innerHTML =
            '<p>Are you sure you want to delete this ' + name + ' with ID: ' +
            deleteFormId +
            '?</p>';
        $('#exampleModalCenter').modal('show');
    }

    function deleteItem() {
        if (deleteFormId !== '') {
            // Perform delete operation or submit the form
            document.getElementById(deleteFormId).submit();
        }
    }
</script>
</body>

</html>