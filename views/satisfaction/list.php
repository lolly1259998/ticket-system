<?php $css_file = 'tickets.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><?php echo $active_type==='satisfait' ? 'Tickets satisfaits' : 'Tickets non satisfaits'; ?></h1>
        <a href="index.php?action=ticket_list" class="btn-primary">Tous les tickets</a>
    </div>

    <div class="tickets-list">
        <div class="ticket-row header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;">
            <div>Sujet</div>
            <div>Tracker</div>
            <div>Statut</div>
            <div>Satisfaction</div>
            <div>Date</div>
            <div>Actions</div>
        </div>

        <?php if(!empty($tickets)): ?>
            <?php foreach($tickets as $ticket): ?>
                <div class="ticket-row" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;">
                    <div class="ticket-title"><?php echo htmlspecialchars($ticket['sujet']); ?></div>
                    <div><?php echo $ticket['tracker']; ?></div>
                    <div>
                        <?php $status_slug_map = ['Nouveau'=>'nouveau','En cours'=>'en-cours','Résolu'=>'resolu','Fermé'=>'ferme']; $slug = $status_slug_map[$ticket['status']] ?? strtolower(str_replace(' ', '-', $ticket['status'])); ?>
                        <span class="ticket-status status-<?php echo $slug; ?>">
                            <?php echo $ticket['status']; ?>
                        </span>
                    </div>
                    <div><?php echo (int)$ticket['degre_satisfaction']; ?>/10</div>
                    <div><?php echo date('d/m/Y', strtotime($ticket['created_at'])); ?></div>
                    <div class="ticket-actions">
                        <a href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>" class="btn-small btn-view" title="Voir" aria-label="Voir">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="color: var(--blue);">
                                <path d="M8 3C4.5 3 2 8 2 8s2.5 5 6 5 6-5 6-5-2.5-5-6-5zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                            </svg>
                        </a>
                        <a href="index.php?action=edit_ticket&id=<?php echo $ticket['id']; ?>" class="btn-small btn-edit" title="Modifier" aria-label="Modifier">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="color: #28a745;">
                                <path d="M12.146.854a.5.5 0 0 1 .708 0l2.292 2.292a.5.5 0 0 1 0 .708L6.5 12.5l-3 1 1-3 6.646-6.646z"/>
                                <path d="M11.5 1.5l3 3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ticket-row">
                <div colspan="6" style="text-align: center; padding: 2rem;">
                    Aucun ticket trouvé.
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if(isset($total_pages) && $total_pages > 1): ?>
    <?php $base = 'index.php?action=satisfaction_list&type=' . urlencode($active_type); ?>
    <div style="display:flex;gap:.5rem;justify-content:center;align-items:center;margin-top:1rem;">
        <?php if($page > 1): ?>
            <a class="btn-small" href="<?php echo $base . '&page=' . ($page-1); ?>">Précédent</a>
        <?php endif; ?>
        <?php for($p=1; $p<=$total_pages; $p++): ?>
            <?php if($p == $page): ?>
                <span class="btn-small" style="background:#0d6efd;color:#fff;"><?php echo $p; ?></span>
            <?php else: ?>
                <a class="btn-small" href="<?php echo $base . '&page=' . $p; ?>"><?php echo $p; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if($page < $total_pages): ?>
            <a class="btn-small" href="<?php echo $base . '&page=' . ($page+1); ?>">Suivant</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'views/layout/footer.php'; ?>
