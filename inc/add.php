<?php
    if(!empty($_REQUEST['val'])) {
        $q = 'insert into cracks (content, owner, datesend) '
                . ' values(:content, :owner, :datesend)';
        $stmt = $db->prepare($q);
        $stmt->bindParam(':content', nl2br($_REQUEST['content']));
        $stmt->bindParam(':owner', $_SESSION['userid']);
        $stmt->bindParam(':datesend', time());
        $stmt->execute();
        // rediriger vers le nouveau crack
        header('Location:index.php?inc=search&cid='.$db->lastInsertId());
        exit;
    }
?><form method="post">
    <div>
        <h2>Ajouter un crack</h2>
        <p>
            <label for="content">
                Contenu
            </label>
            <textarea name="content"
                      id="content"
                      required="required"></textarea>
            <input type="submit" name="val" value="Ajouter ce crack" />
        </p>
    </div>
</form>