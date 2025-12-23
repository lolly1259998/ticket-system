<?php $css_file = 'tickets.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>Modifier le Ticket #<?php echo $ticket['id']; ?></h1>
        <a href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>" class="btn-primary">Retour au ticket</a>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" class="ticket-form">
        <div class="form-row">
            <div class="form-group">
                <label for="tracker">Tracker *</label>
                <select name="tracker" id="tracker" required>
                    <option value="Bug" <?php echo $ticket['tracker'] == 'Bug' ? 'selected' : ''; ?>>Bug</option>
                    <option value="Feature" <?php echo $ticket['tracker'] == 'Feature' ? 'selected' : ''; ?>>Feature</option>
                    <option value="Support" <?php echo $ticket['tracker'] == 'Support' ? 'selected' : ''; ?>>Support</option>
                    <option value="Webmastering" <?php echo $ticket['tracker'] == 'Webmastering' ? 'selected' : ''; ?>>Webmastering</option>
                    <option value="Security" <?php echo $ticket['tracker'] == 'Security' ? 'selected' : ''; ?>>Security</option>
                    <option value="Audit" <?php echo $ticket['tracker'] == 'Audit' ? 'selected' : ''; ?>>Audit</option>
                </select>
            </div>

            <div class="form-group">
                <label for="priority">Priorité *</label>
                <select name="priority" id="priority" required>
                    <option value="Basse" <?php echo $ticket['priority'] == 'Basse' ? 'selected' : ''; ?>>Basse</option>
                    <option value="Normale" <?php echo $ticket['priority'] == 'Normale' ? 'selected' : ''; ?>>Normale</option>
                    <option value="Haute" <?php echo $ticket['priority'] == 'Haute' ? 'selected' : ''; ?>>Haute</option>
                    <option value="Urgente" <?php echo $ticket['priority'] == 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="sujet">Sujet *</label>
            <input type="text" name="sujet" id="sujet" value="<?php echo htmlspecialchars($ticket['sujet']); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($ticket['description']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="assigned_to">Assigné à</label>
                <select name="assigned_to" id="assigned_to">
                    <option value="">Non assigné</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>" 
                            <?php echo $ticket['assigned_to'] == $user['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_debut">Date de début *</label>
                <input type="date" name="date_debut" id="date_debut" value="<?php echo $ticket['date_debut']; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="degre_satisfaction">Degré de satisfaction (0-10)</label>
                <input type="number" name="degre_satisfaction" id="degre_satisfaction" 
                       min="0" max="10" value="<?php echo $ticket['degre_satisfaction']; ?>">
            </div>

            <div class="form-group">
                <label for="fichier">Fichier joint</label>
                <input type="file" name="fichier" id="fichier">
                <?php if($ticket['fichier']): ?>
                    <p style="color: var(--light-gray); font-size: 0.9rem; margin-top: 0.5rem;">
                        Fichier actuel: <?php echo $ticket['fichier']; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn-primary">Mettre à jour le Ticket</button>
    </form>
</div>

<?php include 'views/layout/footer.php'; ?>