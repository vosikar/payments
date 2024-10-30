<?php
    if(!isset($_GET["type"])) die;
    $type = $_GET["type"];
    if(!in_array($type, ["spotify", "youtube"])) die;
    $admin = in_array($_SERVER["REMOTE_ADDR"], ["::1"]);

    $columns = [
        "username" => "Uživatel",
        "service" => "Služba",
        "payment_for" => "Období (rok/měsíc)",
        "state" => "Stav",
    ];
    if($admin){
        $columns["action"] = "Akce";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $type === "spotify" ? "Spotify" : "YouTube" ?> payments</title>

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <!-- Datatables -->
        <link href="https://cdn.datatables.net/v/bs5/dt-2.0.6/datatables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/v/bs5/dt-2.0.6/datatables.min.js"></script>

        <!-- Custom CSS -->
        <style>
            .oval{
                color: white;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                border-radius: 20px;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <table id="payments-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <?php foreach(array_values($columns) as $column): ?>
                            <th><?= $column ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <script>
            if(btoa(prompt("Vložte heslo")) != "M3hzd2Fn"){
                window.location.href = "http://vosikar.cz";
            }

            $(function(){
                $("#payments-table tbody").on("click", ".pay-button", function(e){
                    console.log($(this));
                    //TODO: zprovoznit
                });
                $("#payments-table").dataTable({
                    ajax: {
                        url: "payments.php?type=<?= $type ?>"
                    },
                    columns: [
                        <?php foreach(array_keys($columns) as $column): ?>
                            { "data": "<?= $column ?>" },
                        <?php endforeach; ?>
                    ],
                    columnDefs: [
                        {
                            "targets": [3],
                            render: function(data, type, row, meta){
                                return `<span class="oval ${data === "zaplaceno" ? "bg-success" : "bg-danger"}">${data}</span>`;
                            },
                        },
                    ],
                    order: [[3, "asc"], [2, "desc"]],
                });
            });
        </script>
    </body>
</html>