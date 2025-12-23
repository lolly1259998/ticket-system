<?php $css_file = 'tickets.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>Tous les Tickets</h1>
        <a href="index.php?action=create_ticket" class="btn-primary">Nouveau Ticket</a>
    </div>

    <!-- Filtres Statut -->
    <div class="status-filters">
        <h3 class="section-title">Filtres Statut</h3>
        <a class="status-link reset" href="index.php?action=ticket_list">Tous</a>
        <form method="GET" action="" style="margin-top:.5rem; display:flex; gap:.5rem; align-items:end;">
            <input type="hidden" name="action" value="ticket_list">
            <?php if(!empty($active_status)): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($active_status); ?>">
            <?php endif; ?>
            <div class="form-group" style="max-width:200px;">
                <label>Du</label>
                <input type="date" name="from" value="<?php echo htmlspecialchars($from_date ?? ''); ?>">
            </div>
            <div class="form-group" style="max-width:200px;">
                <label>Au</label>
                <input type="date" name="to" value="<?php echo htmlspecialchars($to_date ?? ''); ?>">
            </div>
            <div class="form-group" style="max-width:80px;">
                <label style="visibility:hidden;">Filtrer</label>
                <button type="submit" class="btn-filter" title="Filtrer" aria-label="Filtrer">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="color: var(--red);">
                        <path d="M1 3h14l-5 5v3.5l-2 1.5V8L1 3z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="tickets-list">
        <div class="ticket-row header">
            <div>Sujet</div>
            <div>Tracker</div>
            <div>Statut</div>
            <div>Date</div>
            <div>Créé par</div>
            <div>Assigné à</div>
            <div>Actions</div>
        </div>

        <?php if(!empty($tickets)): ?>
            <?php foreach($tickets as $ticket): ?>
                <div class="ticket-row">
                    <div class="ticket-title"><?php echo htmlspecialchars($ticket['sujet']); ?></div>
                    <div><?php echo $ticket['tracker']; ?></div>
                    <div>
                        <?php $status_slug_map = ['Nouveau'=>'nouveau','En cours'=>'en-cours','Résolu'=>'resolu','Fermé'=>'ferme']; $slug = $status_slug_map[$ticket['status']] ?? strtolower(str_replace(' ', '-', $ticket['status'])); ?>
                        <span class="ticket-status status-<?php echo $slug; ?>">
                            <?php echo $ticket['status']; ?>
                        </span>
                    </div>
                    <div><?php echo date('d/m/Y', strtotime($ticket['created_at'])); ?></div>
                    <div><?php echo $ticket['created_username']; ?></div>
                    <div><?php echo $ticket['assigned_username'] ?? 'Non assigné'; ?></div>
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
            <?php if(empty($active_status)): ?>
                <div class="ticket-row">
                    <div colspan="6" style="text-align: center; padding: 2rem;">
                        Aucun ticket trouvé.
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if(isset($total_pages) && $total_pages > 1): ?>
    <?php 
        $base = 'index.php?action=ticket_list' 
            . ($active_status ? '&status=' . urlencode($active_status) : '')
            . (!empty($from_date) ? '&from=' . urlencode($from_date) : '')
            . (!empty($to_date) ? '&to=' . urlencode($to_date) : ''); 
    ?>
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
