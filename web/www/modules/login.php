<?php include_once 'header.php'; ?>

<body>
    <div class="modal modal-sheet position-static d-block bg-secondary py-5" tabindex="-1" role="dialog"
        id="modalSheet">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-6 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title"><i class="fa-regular fa-clock"></i> Workhours-Tracker</h5>
                </div>
                <div class="modal-body py-0">
                    <form action="/index.php" method="post" id="login-form">
                        <input type="hidden" name="function" value="login">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Username" name="username">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" placeholder="Password" name="password">
                        </div>
                    </form>
                </div>
                <div class="modal-footer flex-column border-top-0">
                    <button type="submit" class="btn btn-lg btn-primary w-100 mx-0 mb-2" form="login-form">Anmelden</button>
                    <button type="reset" class="btn btn-lg btn-light w-100 mx-0" data-bs-dismiss="modal">Abbrechen</button>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'footer.php'; ?>
</body>