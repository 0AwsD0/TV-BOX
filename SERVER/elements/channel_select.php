<div class="row info">
    <div class="col info-col">
        <p>Select channel to Edit</p>
        <form action="display.php" method="post">
            <select name="channel" id="channel" style="width: 100%; max-width: 700px;">
                <?php
                    require("./pdo.php");
                        $sql = "SELECT channels.name, channels.id FROM channels;";
                        $select = $pdo->query($sql);
                    foreach($select->fetchAll(PDO::FETCH_ASSOC) as $k=>$v){
                        echo('<option value="'.htmlentities($v["name"]).'">'.htmlentities($v["name"]).' -  DB id: '.htmlentities($v["id"]).'</option>');
                    }
                ?>
            </select>
        <button class="file-button" type="submit" style="padding: 10px; font-size: 25px;">Select channel</button>
        </form>
    </div>
</div>