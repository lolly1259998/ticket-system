<?php $css_file = 'tickets.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>Top intervenants</h1>
    </div>

    <form method="GET" action="" style="margin-bottom:1rem; display:flex; gap:.5rem; align-items:end;">
        <input type="hidden" name="action" value="satisfaction_stats">
        <div class="form-group" style="max-width:200px;">
            <label>Du</label>
            <input type="date" name="from" value="<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>">
        </div>
        <div class="form-group" style="max-width:200px;">
            <label>Au</label>
            <input type="date" name="to" value="<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn-primary">Filtrer</button>
    </form>

    <?php if(isset($summary) && $summary): ?>
    <div class="tickets-list" style="margin-bottom:1rem;">
        <div class="ticket-row header" style="grid-template-columns: 1fr 1fr;">
            <div>Total tickets avec avis</div>
            <div>Satisfaction moyenne globale</div>
        </div>
        <div class="ticket-row" style="grid-template-columns: 1fr 1fr;">
            <div><?php echo (int)$summary['total']; ?></div>
            <div><?php echo number_format((float)$summary['avg_satisfaction'],2); ?>/10</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!empty($stats)): ?>
    <?php 
        $total = 0; 
        foreach($stats as $s){ $total += (int)$s['tickets_count']; }
        $colors = ['#FFB3BA','#BAE1FF','#BFFCC6','#FFDFBA','#FFFFBA','#C7CEEA','#E2F0CB','#F1CBFF'];
        $cx = 160; $cy = 160; $r = 140; $angle = 0; $i = 0;
    ?>
    <div style="display:grid; grid-template-columns: 360px 1fr; gap:1.5rem; align-items:start;">
        <div style="position:relative;">
            <svg width="320" height="320">
                <?php foreach($stats as $row): ?>
                    <?php 
                        $count = (int)$row['tickets_count'];
                        if($count <= 0) continue;
                        $portion = $count / max($total,1);
                        $span = $portion * 360;
                        $start = deg2rad($angle);
                        $end = deg2rad($angle + $span);
                        $x1 = $cx + $r * cos($start);
                        $y1 = $cy + $r * sin($start);
                        $x2 = $cx + $r * cos($end);
                        $y2 = $cy + $r * sin($end);
                        $large = ($span > 180) ? 1 : 0;
                        $fill = $colors[$i % count($colors)];
                        $avatar = '';
                        if(!empty($row['avatar'])) {
                          $avatar = $basePath . 'uploads/' . htmlspecialchars($row['avatar']);
                        } else if(isset($_SESSION['user_id']) && isset($row['user_id']) && $_SESSION['user_id'] == $row['user_id'] && !empty($_SESSION['user_avatar'])) {
                          $avatar = $basePath . 'uploads/' . htmlspecialchars($_SESSION['user_avatar']);
                        } else if(isset($row['email']) && $row['email']) {
                          $hash = md5(strtolower(trim($row['email'])));
                          $avatar = 'https://www.gravatar.com/avatar/' . $hash . '?s=64&d=mp';
                        }
                    ?>
                    <path d="M <?php echo $cx; ?> <?php echo $cy; ?> L <?php echo $x1; ?> <?php echo $y1; ?> A <?php echo $r; ?> <?php echo $r; ?> 0 <?php echo $large; ?> 1 <?php echo $x2; ?> <?php echo $y2; ?> Z" fill="<?php echo $fill; ?>" data-username="<?php echo htmlspecialchars($row['username'] ?: 'Utilisateur inconnu'); ?>" data-count="<?php echo (int)$row['tickets_count']; ?>" data-avg="<?php echo number_format((float)$row['avg_satisfaction'],2); ?>" data-avatar="<?php echo htmlspecialchars($avatar); ?>" class="slice"></path>
                    <?php $angle += $span; $i++; ?>
                <?php endforeach; ?>
            </svg>
            <div id="chartTooltip" style="position:absolute; display:none; background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); padding:.5rem .75rem; min-width:180px; z-index:10;"></div>
        </div>
        <div>
            <div class="tickets-list">
                <div class="ticket-row header" style="grid-template-columns: 2fr 1fr 2fr;">
                    <div>Utilisateur</div>
                    <div>Tickets</div>
                    <div>Satisfaction moyenne</div>
                </div>
                <?php foreach($stats as $row): ?>
                    <div class="ticket-row" style="grid-template-columns: 2fr 1fr 2fr;">
                        <div class="ticket-title" style="display:flex; align-items:center; gap:.5rem;">
                            <?php 
                                $rowAvatar = '';
                                if(!empty($row['avatar'])) { 
                                    $rowAvatar = $basePath . 'uploads/' . htmlspecialchars($row['avatar']); 
                                } else if(isset($_SESSION['user_id']) && isset($row['user_id']) && $_SESSION['user_id'] == $row['user_id'] && !empty($_SESSION['user_avatar'])) { 
                                    $rowAvatar = $basePath . 'uploads/' . htmlspecialchars($_SESSION['user_avatar']); 
                                } else if(isset($row['email']) && $row['email']) { 
                                    $rowAvatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($row['email']))) . '?s=64&d=mp'; 
                                }
                            ?>
                            <?php if(!empty($rowAvatar)): ?>
                                <img src="<?php echo htmlspecialchars($rowAvatar); ?>" alt="avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <i class="fas fa-user" style="color:#6c757d;"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($row['username'] ?: 'Utilisateur inconnu'); ?>
                        </div>
                        <div><?php echo (int)$row['tickets_count']; ?></div>
                        <div>
                            <div style="background:rgba(2,39,68,0.08); border:1px solid var(--border); border-radius:999px; overflow:hidden; height:10px;">
                                <div style="width: <?php echo min(max((float)$row['avg_satisfaction']*10,0),100); ?>%; height:10px; background:#9EE493;"></div>
                            </div>
                            <span style="font-size:.9rem; color:#495057;"><?php echo number_format((float)$row['avg_satisfaction'],2); ?>/10</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
    (function(){
      var tip = document.getElementById('chartTooltip');
      document.querySelectorAll('.slice').forEach(function(el){
        el.addEventListener('mousemove', function(e){
          var u = this.getAttribute('data-username');
          var c = this.getAttribute('data-count');
          var a = this.getAttribute('data-avg');
          var av = this.getAttribute('data-avatar');
          var imgHtml = av ? ('<img src="'+av+'" style="width:28px;height:28px;border-radius:50%;object-fit:cover;margin-right:.5rem;"/>') : '<i class="fas fa-user" style="color:#6c757d;margin-right:.5rem;"></i>';
          tip.innerHTML = '<div style="display:flex;align-items:center;gap:.5rem;">'+ imgHtml + '<div><div style="font-weight:700;color:#022744;">'+u+'</div><div style="color:#495057;">Tickets: '+c+' • Moyenne: '+a+'/10</div></div></div>';
          tip.style.left = (e.offsetX + 12) + 'px';
          tip.style.top = (e.offsetY + 12) + 'px';
          tip.style.display = 'block';
        });
        el.addEventListener('mouseleave', function(){ tip.style.display = 'none'; });
      });
    })();
    </script>
    <?php else: ?>
    <div class="tickets-list">
        <div class="ticket-row">
            <div colspan="4" style="text-align: center; padding: 2rem;">Aucune donnée de satisfaction.</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'views/layout/footer.php'; ?>
