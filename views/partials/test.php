<!-- Order Line -->
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Button -->
<button id="trigger-loading-1" class="btn btn-default m-r-5">
    <i class="anticon anticon-loading m-r-5"></i>
    <i class="anticon anticon-poweroff m-r-5"></i>
    <span>Click Me</span>
</button>
<script>
    $('#trigger-loading-1').on('click', function(e) {
    $(this).addClass("is-loading");
    setTimeout(function() { $("#trigger-loading-1").removeClass("is-loading");}, 4000);
    e.preventDefault();
    });
</script>