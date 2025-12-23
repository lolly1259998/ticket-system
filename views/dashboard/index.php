<?php $css_file = 'dashboard.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content">
    <div class="dashboard-header">
        <h1>Tableau de Bord</h1>
        <p>Bienvenue, <?php echo $_SESSION['username']; ?>!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['total']; ?></span>
            <span class="stat-label">Total Tickets</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['nouveau']; ?></span>
            <span class="stat-label">Nouveaux</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['en_cours']; ?></span>
            <span class="stat-label">En Cours</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['resolu']; ?></span>
            <span class="stat-label">Résolus</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['ferme']; ?></span>
            <span class="stat-label">Fermés</span>
        </div>
    </div>

    <div class="quick-actions">
        <a href="index.php?action=create_ticket" class="action-btn">Nouveau Ticket</a>
        <a href="index.php?action=ticket_list" class="action-btn">Mes Tickets</a>
        <a href="index.php?action=ticket_list&status=Nouveau" class="action-btn">Tickets Nouveaux</a>
        <a href="index.php?action=ticket_list&status=En cours" class="action-btn">Tickets en Cours</a>
    </div>

    <div class="recent-tickets">
        <h3>Tickets Récents</h3>
        <?php if(!empty($tickets)): ?>
            <?php foreach(array_slice($tickets, 0, 5) as $ticket): ?>
                <div class="ticket-item">
                    <div class="ticket-header">
                        <span class="ticket-title"><?php echo htmlspecialchars($ticket['sujet']); ?></span>
                        <?php $status_slug_map = ['Nouveau'=>'nouveau','En cours'=>'en-cours','Résolu'=>'resolu','Fermé'=>'ferme']; $slug = $status_slug_map[$ticket['status']] ?? strtolower(str_replace(' ', '-', $ticket['status'])); ?>
                        <span class="ticket-status status-<?php echo $slug; ?>">
                            <?php echo $ticket['status']; ?>
                        </span>
                    </div>
                    <div class="ticket-meta">
                        Tracker: <?php echo $ticket['tracker']; ?> | 
                        Priorité: <?php echo $ticket['priority']; ?> | 
                        Créé le: <?php echo date('d/m/Y', strtotime($ticket['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun ticket trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
