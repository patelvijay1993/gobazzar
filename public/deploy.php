<?php
if (($_GET['key'] ?? '') !== 'gobazzar-deploy-2026') { http_response_code(403); die('Forbidden'); }

$base    = dirname(__DIR__);
$gitBase = $base . '/gobazzar-git'; // cPanel git clone path
$useGit  = is_dir($gitBase . '/.git') ? $gitBase : $base;

function run($cmd, $base) {
    $out = shell_exec("cd " . escapeshellarg($base) . " && $cmd 2>&1");
    return trim($out ?: '');
}

$action = $_GET['action'] ?? 'status';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GoBazaar Deploy</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#0f1117;color:#e2e8f0;min-height:100vh;padding:24px}
.wrap{max-width:860px;margin:0 auto}
.header{display:flex;align-items:center;gap:14px;margin-bottom:28px}
.logo{font-size:28px;font-weight:800;color:#fff}
.logo span{color:#f59e0b}
.badge{background:#1e293b;border:1px solid #334155;border-radius:6px;padding:4px 12px;font-size:12px;color:#94a3b8}
.card{background:#1e293b;border:1px solid #334155;border-radius:12px;margin-bottom:20px;overflow:hidden}
.card-head{padding:16px 20px;border-bottom:1px solid #334155;display:flex;align-items:center;gap:10px}
.card-head h3{font-size:15px;font-weight:600;color:#f1f5f9}
.card-head .icon{font-size:18px}
.card-body{padding:20px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .15s;text-decoration:none}
.btn-primary{background:#3b82f6;color:#fff}
.btn-primary:hover{background:#2563eb}
.btn-danger{background:#ef4444;color:#fff}
.btn-danger:hover{background:#dc2626}
.btn-green{background:#10b981;color:#fff}
.btn-green:hover{background:#059669}
.btn-gray{background:#334155;color:#e2e8f0}
.btn-gray:hover{background:#475569}
.status-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #1e293b}
.status-row:last-child{border-bottom:none}
.dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.dot-green{background:#10b981}
.dot-red{background:#ef4444}
.dot-yellow{background:#f59e0b}
.file-list{background:#0f1117;border-radius:8px;padding:14px;font-family:monospace;font-size:12.5px;max-height:300px;overflow-y:auto}
.file-item{display:flex;align-items:center;gap:8px;padding:4px 0;border-bottom:1px solid #1a2235}
.file-item:last-child{border-bottom:none}
.file-badge{font-size:10px;padding:2px 7px;border-radius:4px;font-weight:700;flex-shrink:0}
.f-add{background:#064e3b;color:#34d399}
.f-mod{background:#1e3a5f;color:#60a5fa}
.f-del{background:#4c0519;color:#f87171}
.commit-item{padding:12px 0;border-bottom:1px solid #334155}
.commit-item:last-child{border-bottom:none}
.commit-hash{font-family:monospace;font-size:11px;color:#f59e0b;background:#292524;padding:2px 7px;border-radius:4px}
.commit-msg{font-size:13.5px;color:#e2e8f0;margin:4px 0}
.commit-meta{font-size:11px;color:#64748b}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px}
.alert-success{background:#064e3b;border:1px solid #065f46;color:#34d399}
.alert-error{background:#4c0519;border:1px solid #7f1d1d;color:#f87171}
.alert-info{background:#1e3a5f;border:1px solid #1e40af;color:#93c5fd}
.log-box{background:#0f1117;border-radius:8px;padding:14px;font-family:monospace;font-size:12px;color:#4ade80;max-height:400px;overflow-y:auto;white-space:pre-wrap;word-break:break-all}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.stat-box{background:#0f1117;border-radius:8px;padding:16px;text-align:center}
.stat-num{font-size:28px;font-weight:800;color:#f59e0b}
.stat-label{font-size:12px;color:#64748b;margin-top:4px}
@media(max-width:600px){.grid2{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">

<div class="header">
  <div class="logo">Go<span>Bazaar</span></div>
  <div class="badge">Deploy Panel</div>
  <div class="badge" style="margin-left:auto;color:#f59e0b"><?= date('d M Y, H:i') ?></div>
</div>

<?php
// ── Get current git info ──────────────────────────────────────────────
$currentBranch  = run('git rev-parse --abbrev-ref HEAD', $useGit);
$localCommit    = run('git rev-parse --short HEAD', $useGit);
$localCommitFull= run('git rev-parse HEAD', $useGit);
$lastCommitMsg  = run('git log -1 --pretty=format:"%s"', $useGit);
$lastCommitTime = run('git log -1 --pretty=format:"%ar"', $useGit);
$lastCommitAuth = run('git log -1 --pretty=format:"%an"', $useGit);

// Fetch remote to compare
run('git fetch origin 2>&1', $useGit);
$remoteCommit     = run('git rev-parse --short origin/main', $useGit);
$remoteCommitFull = run('git rev-parse origin/main', $useGit);
$isUpToDate       = ($localCommitFull === $remoteCommitFull);

// Files that will change on pull
$pendingFiles = run('git diff --name-status HEAD origin/main', $useGit);
$pendingLines = $pendingFiles ? array_filter(explode("\n", $pendingFiles)) : [];
$pendingCount = count($pendingLines);

// Last 5 commits on remote
$remoteLog      = run('git log origin/main -5 --pretty=format:"%h|||%s|||%an|||%ar"', $useGit);
$remoteLogLines = $remoteLog ? array_filter(explode("\n", $remoteLog)) : [];
?>

<?php if ($action === 'pull'): ?>
<?php
  // ── DO THE PULL ────────────────────────────────────────────────────
  $pullLog = '';

  // 1. Pull into git repo folder
  $pullLog .= "--- Git Pull ($useGit) ---\n";
  $pullLog .= run('git fetch origin', $useGit) . "\n";
  $pullLog .= run('git reset --hard origin/main', $useGit) . "\n";
  $pullLog .= run('git pull origin main', $useGit) . "\n\n";

  // 2. If git repo is in subfolder (gobazzar-git), copy files to site root
  if ($useGit !== $base) {
      $pullLog .= "--- Copying files from gobazzar-git to site root ---\n";
      // Copy all except .git folder and public/index.php (already exists)
      $rsync = shell_exec("cd " . escapeshellarg($useGit) . " && rsync -a --exclude='.git' --exclude='node_modules' --exclude='.env' . " . escapeshellarg($base . '/') . " 2>&1");
      $pullLog .= ($rsync ?: 'Files copied OK') . "\n\n";
  }

  // 3. Artisan commands on site root
  $pullLog .= "--- Artisan ---\n";
  $pullLog .= run('php artisan config:clear', $base) . "\n";
  $pullLog .= run('php artisan route:clear', $base) . "\n";
  $pullLog .= run('php artisan view:clear', $base) . "\n";
  $pullLog .= run('php artisan cache:clear', $base) . "\n";
  $pullLog .= run('php artisan migrate --force', $base) . "\n";
  $pullLog .= run('php artisan config:cache', $base) . "\n";
  $pullLog .= run('php artisan route:cache', $base) . "\n";
  $pullLog .= run('php artisan view:cache', $base) . "\n";
  $newCommit = run('git rev-parse --short HEAD', $useGit);
?>
  <div class="alert alert-success">✅ Deploy complete! Now on <strong><?= htmlspecialchars($newCommit) ?></strong></div>
  <div class="card">
    <div class="card-head"><span class="icon">📋</span><h3>Deploy Log</h3></div>
    <div class="card-body">
      <div class="log-box"><?= htmlspecialchars($pullLog) ?></div>
    </div>
  </div>
  <a href="/deploy.php?key=gobazzar-deploy-2026" class="btn btn-gray">← Back to Status</a>

<?php else: ?>

<!-- STATUS CARDS -->
<div class="grid2" style="margin-bottom:20px">
  <div class="stat-box">
    <div class="stat-num"><?= htmlspecialchars($localCommit) ?></div>
    <div class="stat-label">Current (Production)</div>
    <div style="font-size:11px;color:#64748b;margin-top:4px"><?= htmlspecialchars($lastCommitTime) ?></div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:<?= $isUpToDate ? '#10b981' : '#f59e0b' ?>"><?= htmlspecialchars($remoteCommit) ?></div>
    <div class="stat-label">Latest (GitHub)</div>
    <div style="font-size:11px;color:<?= $isUpToDate ? '#10b981' : '#f59e0b' ?>;margin-top:4px">
      <?= $isUpToDate ? '✅ Up to date' : "⚠️ $pendingCount file(s) pending" ?>
    </div>
  </div>
</div>

<!-- CURRENT STATUS -->
<div class="card">
  <div class="card-head"><span class="icon">🖥️</span><h3>Production Status</h3></div>
  <div class="card-body">
    <div class="status-row">
      <div class="dot dot-green"></div>
      <div style="flex:1;font-size:13px">Branch</div>
      <div style="font-family:monospace;color:#f59e0b"><?= htmlspecialchars($currentBranch) ?></div>
    </div>
    <div class="status-row">
      <div class="dot dot-green"></div>
      <div style="flex:1;font-size:13px">Current Commit</div>
      <div style="font-family:monospace;color:#f59e0b"><?= htmlspecialchars($localCommit) ?></div>
    </div>
    <div class="status-row">
      <div class="dot dot-green"></div>
      <div style="flex:1;font-size:13px">Last Deploy</div>
      <div style="font-size:13px;color:#94a3b8"><?= htmlspecialchars($lastCommitMsg) ?></div>
    </div>
    <div class="status-row">
      <div class="dot dot-<?= $isUpToDate ? 'green' : 'yellow' ?>"></div>
      <div style="flex:1;font-size:13px">Status</div>
      <div style="font-size:13px;color:<?= $isUpToDate ? '#10b981' : '#f59e0b' ?>">
        <?= $isUpToDate ? '✅ Up to date' : "⚠️ $pendingCount new file(s) to pull" ?>
      </div>
    </div>
  </div>
</div>

<?php if (!$isUpToDate && $pendingCount > 0): ?>
<!-- PENDING FILES -->
<div class="card">
  <div class="card-head"><span class="icon">📁</span><h3>Files to be Updated (<?= $pendingCount ?>)</h3></div>
  <div class="card-body">
    <div class="file-list">
      <?php foreach ($pendingLines as $line):
        $parts = explode("\t", $line);
        $status = $parts[0] ?? '';
        $file   = $parts[1] ?? $line;
        $badgeClass = str_starts_with($status,'A') ? 'f-add' : (str_starts_with($status,'D') ? 'f-del' : 'f-mod');
        $badgeText  = str_starts_with($status,'A') ? 'NEW' : (str_starts_with($status,'D') ? 'DEL' : 'MOD');
      ?>
      <div class="file-item">
        <span class="file-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
        <span style="color:#e2e8f0"><?= htmlspecialchars($file) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:16px">
      <a href="/deploy.php?key=gobazzar-deploy-2026&action=pull"
         onclick="return confirm('Deploy karo? Production update ho jayega.')"
         class="btn btn-green">
        🚀 Deploy Now (Pull + Cache Clear)
      </a>
    </div>
  </div>
</div>
<?php else: ?>
<div class="alert alert-success">✅ Production is up to date! Koi pending changes nahi hain.</div>
<a href="/deploy.php?key=gobazzar-deploy-2026&action=pull"
   onclick="return confirm('Force deploy karo?')"
   class="btn btn-gray" style="margin-bottom:20px">🔄 Force Redeploy Anyway</a>
<?php endif; ?>

<!-- RECENT COMMITS -->
<div class="card">
  <div class="card-head"><span class="icon">📝</span><h3>Recent Commits (GitHub)</h3></div>
  <div class="card-body">
    <?php foreach ($remoteLogLines as $line):
      $parts = explode('|||', $line);
      $hash  = $parts[0] ?? '';
      $msg   = $parts[1] ?? '';
      $auth  = $parts[2] ?? '';
      $time  = $parts[3] ?? '';
      $isCurrent = ($hash === $localCommit);
    ?>
    <div class="commit-item">
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <span class="commit-hash"><?= htmlspecialchars($hash) ?></span>
        <?php if ($isCurrent): ?>
        <span style="background:#064e3b;color:#34d399;font-size:10px;padding:2px 8px;border-radius:4px;font-weight:700">● LIVE</span>
        <?php endif; ?>
      </div>
      <div class="commit-msg"><?= htmlspecialchars($msg) ?></div>
      <div class="commit-meta"><?= htmlspecialchars($auth) ?> · <?= htmlspecialchars($time) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

</div>
</body>
</html>
