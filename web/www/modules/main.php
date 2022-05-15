<?php
    function print_Overtime($value) {
        $style = '';
        if ($value < 0) {
            $style = 'class="table-danger" style="color: red"';
        } elseif ($value > 0) {
            $style = 'class="table-success" style="color: green"';
        }
    
        print('<td '.$style.'>'.$value.'</td>');
    }

    $month_year = $database->query('SELECT DATE_FORMAT(date, "%b %y") as date FROM workhours.tracker WHERE user_id = '.$user_id.' group by MONTH(date);');

    $month_report = $database->query(
        'SELECT 
            id_tracker,
            DATE_FORMAT(date, "%d.%m.%Y") as date,
            TIME_FORMAT(start_time, "%H:%i") as start_time, 
            TIME_FORMAT(end_time, "%H:%i") as end_time,
            TIME_FORMAT(overtime, "%H:%i") as overtime,
            TIME_FORMAT(overtime_total, "%H:%i") as overtime_total
        FROM 
            tracker
        LFET JOIN
            tracker_overtime
        ON
            id_tracker = tracker_id
        WHERE
            DATE_FORMAT(date, "%b %y") = '.$date.' AND
            user_id = '.$user_id.'
        ORDER BY 
            date');
?>


<?php include_once 'header.php'; ?>

<body>
    <div class="container">
        <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none me-5">
                <span class="fs-4"><i class="fa-regular fa-clock"></i> Workhours-Tracker</span>
            </a>
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <form action="/index.php" method="post" id="logout-form">
                        <input type="hidden" name="function" value="logout">
                        <button type="submit" class="btn btn-danger" form="logout-form">Abmelden</button>
                    </form>
                </li>
            </ul>
        </header>
    </div>

    <main>
        <div class="py-5 bg-light">
            <div class="container">
                <form action="/index.php" method="post" id="insert-form">
                    <input type="hidden" name="function" value="insert-entry">
                    <div class="row">
                        <div class="col-sm-12 col-lg-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="date-label">Datum</span>
                                <input type="date" class="form-control" aria-describedby="date-label" name="date" value="<?php print(date("Y-m-d")); ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="start-time-label">Start Zeit</span>
                                <input type="time" class="form-control" aria-describedby="start-time-label" name="start-time">
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="end-time-label">End Zeit</span>
                                <input type="time" class="form-control" aria-describedby="end-time-label" name="end-time">
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-3 d-grid gap-2 d-md-block">
                                <input type="submit" class="btn btn-primary" value="Hinzufügen"></button>
                                <button type="reset" class="btn btn-secondary">Abbrechen</button>
                        </div>
                    </div>
                </form>
                <div class="row justify-content-end">
                    <div class="col-sm-12 col-lg-3 align-self-end">
                        <form action="/index.php" method="get" id="date-form">
                            <select class="form-select form-select-sm" name="date" onchange="document.getElementById('date-form').submit();">
                                <?php
                                    $entries = $month_year->fetch_all(MYSQLI_ASSOC);
                                    for ($i = 0; $i != sizeof($entries); $i++) {
                                        $selected = '';
                                        if (str_contains($date, $entries[$i]['date']) || !isset($_GET["date"]) && $i == sizeof($entries) -1)
                                            $selected = 'selected';
                                        print('<option value="'.$entries[$i]['date'].'" '.$selected.'>'.$entries[$i]['date'].'</option>');
                                    }
                                ?>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table id="table_id" class="table">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Anfang</th>
                                        <th>Ende</th>
                                        <th>Differenz</th>
                                        <th>Differenz (Gesamt)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $overtime_total = "00:00";
                                        while ($entry = $month_report->fetch_assoc()) {
                                            $overtime_total = $entry['overtime_total'];
                                            
                                            print('<tr>');
                                            print('<td>'.$entry['date'].'</td>');
                                            print('<td>'.$entry['start_time'].'</td>');
                                            print('<td>'.$entry['end_time'].'</td>');
                                            print_Overtime($entry['overtime']);
                                            print_Overtime($entry['overtime_total']);
                                            print(' <td>
                                                        <form action="index.php" method="post" id="form-delete-'.$entry['id_tracker'].'" class="mb-0">
                                                            <input type="hidden" name="function" value="delete-entry">
                                                            <input type="hidden" name="tracker-id" value="'.$entry['id_tracker'].'">
                                                            <input class="btn btn-outline-danger" type="submit" value="Löschen" form="form-delete-'.$entry['id_tracker'].'">
                                                        </form>
                                                    </td>');
                                            print('</tr>');
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">Differenz</td>
                                        <?php print_Overtime($overtime_total); ?>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once 'footer.php'; ?>
    <script>
        $(document).ready(function () {
            $('#table_id').DataTable({
                "columnDefs": [
                    { "searchable": false, "targets": _all }
                ]
            });
        });
    </script>
</body>