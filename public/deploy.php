<?php
if (($_GET['key'] ?? '') !== 'gobazzar-deploy-2026') { http_response_code(403); die('Forbidden'); }

$base    = dirname(__DIR__);

// Find gobazzar-git folder (cPanel clones here)
$gitPaths = [
    $base . '/gobazzar-git',
    '/home/heavendw/public_html/gobazzarweb.heavendwell.com/gobazzar-git',
    dirname($base) . '/gobazzar-git',
];
$useGit = null;
foreach ($gitPaths as $p) {
    if (is_dir($p)) { $useGit = $p; break; }
}

// Find .git dir — cPanel may store it separately
$gitDir = null;
if ($useGit) {
    if (is_dir($useGit . '/.git'))          $gitDir = $useGit . '/.git';
    elseif (is_file($useGit . '/.git'))     $gitDir = trim(file_get_contents($useGit . '/.git')); // git worktree pointer
}

function run($cmd, $dir) {
    $out = shell_exec("cd " . escapeshellarg($dir) . " && $cmd 2>&1");
    return trim($out ?: '');
}

function gitRun($cmd, $useGit, $gitDir) {
    if (!$useGit) return 'ERROR: gobazzar-git not found';
    if ($gitDir && is_dir($gitDir)) {
        $out = shell_exec("git --git-dir=" . escapeshellarg($gitDir) . " --work-tree=" . escapeshellarg($useGit) . " $cmd 2>&1");
    } else {
        $out = shell_exec("cd " . escapeshellarg($useGit) . " && git $cmd 2>&1");
    }
    return trim($out ?: '');
}

// GitHub API — get latest commit without local git
$githubRepo   = 'vijaypateldeveloper/gobazzar-app'; // UPDATE THIS if different
$githubBranch = 'main';
$apiUrl       = "https://api.github.com/repos/$githubRepo/commits/$githubBranch";
$ctx = stream_context_create(['http'=>['header'=>"User-Agent: GoBazaarDeploy/1.0\r\n",'timeout'=>8]]);
$apiResp = @file_get_contents($apiUrl, false, $ctx);
$apiData = $apiResp ? json_decode($apiResp, true) : null;
$remoteCommit     = $apiData['sha'] ?? null;
$remoteCommitShort= $remoteCommit ? substr($remoteCommit, 0, 7) : 'N/A';
$remoteMsg        = $apiData['commit']['message'] ?? 'N/A';
$remoteAuthor     = $apiData['commit']['author']['name'] ?? 'N/A';
$remoteTime       = $apiData['commit']['author']['date'] ?? '';
$remoteTimeHuman  = $remoteTime ? date('d M Y H:i', strtotime($remoteTime)) : '';

// Local commit from gobazzar-git
$localCommitFull  = $useGit ? gitRun('rev-parse HEAD', $useGit, $gitDir) : 'N/A';
$localCommit      = (strlen($localCommitFull) >= 7) ? substr($localCommitFull, 0, 7) : $localCommitFull;
$currentBranch    = $useGit ? gitRun('rev-parse --abbrev-ref HEAD', $useGit, $gitDir) : 'N/A';
$lastCommitMsg    = $useGit ? gitRun('log -1 --pretty=format:"%s"', $useGit, $gitDir) : 'N/A';
$lastCommitTime   = $useGit ? gitRun('log -1 --pretty=format:"%ar"', $useGit, $gitDir) : 'N/A';

$isUpToDate = $remoteCommit && $localCommitFull && str_starts_with($localCommitFull, substr($remoteCommit, 0, 7));

// Get pending files from GitHub API compare
$compareUrl  = "https://api.github.com/repos/$githubRepo/compare/{$localCommitFull}...{$remoteCommit}";
$compareResp = ($remoteCommit && $localCommitFull && strlen($localCommitFull) > 10)
    ? @file_get_contents($compareUrl, false, $ctx) : null;
$compareData = $compareResp ? json_decode($compareResp, true) : null;
$pendingFiles = $compareData['files'] ?? [];
$pendingCount = count($pendingFiles);

// Last 5 commits from GitHub API
$commitsUrl  = "https://api.github.com/repos/$githubRepo/commits?sha=$githubBranch&per_page=5";
$commitsResp = @file_get_contents($commitsUrl, false, $ctx);
$commitsData = $commitsResp ? json_decode($commitsResp, true) : [];

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
// Debug info (shown in pull log, not status)
$debugInfo  = "gobazzar-git path: " . ($useGit ?: 'NOT FOUND') . "\n";
$debugInfo .= ".git dir: " . ($gitDir ?: 'NOT FOUND') . "\n";
$debugInfo .= "Local commit: $localCommit\n";
$debugInfo .= "Remote commit: $remoteCommitShort\n";
$debugInfo .= "GitHub repo: $githubRepo\n";
?>

<?php if ($action === 'pull'): ?>
<?php
  $pullLog = $debugInfo . "\n";

  // 1. Git pull in gobazzar-git
  if ($useGit) {
      $pullLog .= "--- Git Pull in gobazzar-git ---\n";
      $pullLog .= gitRun('fetch origin', $useGit, $gitDir) . "\n";
      $pullLog .= gitRun('reset --hard origin/main', $useGit, $gitDir) . "\n";
      $newLocalCommit = gitRun('rev-parse --short HEAD', $useGit, $gitDir);
      $pullLog .= "Now at: $newLocalCommit\n\n";

      // 2. Copy files to site root (cp -rf, rsync not available on shared hosting)
      $pullLog .= "--- Copying files from gobazzar-git to site root ---\n";
      $dirs = ['app','bootstrap','config','database','public','resources','routes','vendor'];
      foreach ($dirs as $d) {
          $src = escapeshellarg($useGit . '/' . $d);
          $dst = escapeshellarg($base . '/');
          $out = shell_exec("/bin/cp -rf $src $dst 2>&1");
          $pullLog .= "cp $d: " . ($out ?: 'OK') . "\n";
      }
      // Copy root files
      foreach (['artisan','composer.json','.gitignore'] as $f) {
          if (file_exists($useGit . '/' . $f)) {
              $out = shell_exec("/bin/cp -f " . escapeshellarg($useGit.'/'.$f) . " " . escapeshellarg($base.'/'.$f) . " 2>&1");
              $pullLog .= "cp $f: " . ($out ?: 'OK') . "\n";
          }
      }
      $pullLog .= "\n";
  } else {
      $pullLog .= "WARNING: gobazzar-git folder not found! Only running artisan.\n\n";
      $newLocalCommit = 'unknown';
  }

  // 3. Artisan
  $pullLog .= "--- Artisan ---\n";
  $pullLog .= run('php artisan config:clear', $base) . "\n";
  $pullLog .= run('php artisan route:clear', $base) . "\n";
  $pullLog .= run('php artisan view:clear', $base) . "\n";
  $pullLog .= run('php artisan cache:clear', $base) . "\n";
  $pullLog .= run('php artisan migrate --force', $base) . "\n";
  $pullLog .= run('php artisan config:cache', $base) . "\n";
  $pullLog .= run('php artisan route:cache', $base) . "\n";
  $pullLog .= run('php artisan view:cache', $base) . "\n";
?>
  <div class="alert alert-success">Deploy complete! Now on <strong><?= htmlspecialchars($newLocalCommit) ?></strong></div>
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
    <div class="stat-label">Current (gobazzar-git)</div>
    <div style="font-size:11px;color:#64748b;margin-top:4px"><?= htmlspecialchars($lastCommitTime) ?></div>
  </div>
  <div class="stat-box">
    <div class="stat-num" style="color:<?= $isUpToDate ? '#10b981' : '#f59e0b' ?>"><?= htmlspecialchars($remoteCommitShort) ?></div>
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
      <div class="dot dot-<?= $useGit ? 'green' : 'red' ?>"></div>
      <div style="flex:1;font-size:13px">gobazzar-git</div>
      <div style="font-family:monospace;color:#94a3b8;font-size:12px"><?= htmlspecialchars($useGit ?: 'NOT FOUND') ?></div>
    </div>
    <div class="status-row">
      <div class="dot dot-green"></div>
      <div style="flex:1;font-size:13px">Branch</div>
      <div style="font-family:monospace;color:#f59e0b"><?= htmlspecialchars($currentBranch) ?></div>
    </div>
    <div class="status-row">
      <div class="dot dot-green"></div>
      <div style="flex:1;font-size:13px">Local Commit (gobazzar-git)</div>
      <div style="font-family:monospace;color:#f59e0b"><?= htmlspecialchars($localCommit) ?></div>
    </div>
    <div class="status-row">
      <div class="dot dot-green"></div>
      <div style="flex:1;font-size:13px">Last Commit</div>
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
      <?php foreach ($pendingFiles as $f):
        $status    = $f['status'] ?? 'modified';
        $filename  = $f['filename'] ?? '';
        $badgeClass = ($status === 'added') ? 'f-add' : (($status === 'removed') ? 'f-del' : 'f-mod');
        $badgeText  = ($status === 'added') ? 'NEW' : (($status === 'removed') ? 'DEL' : 'MOD');
      ?>
      <div class="file-item">
        <span class="file-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
        <span style="color:#e2e8f0"><?= htmlspecialchars($filename) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:16px">
      <a href="/deploy.php?key=gobazzar-deploy-2026&action=pull"
         onclick="return confirm('Deploy karo? Production update ho jayega.')"
         class="btn btn-green">
        🚀 Deploy Now (Pull + rsync + Cache Clear)
      </a>
    </div>
  </div>
</div>
<?php else: ?>
<div class="alert alert-success">✅ Production is up to date!</div>
<a href="/deploy.php?key=gobazzar-deploy-2026&action=pull"
   onclick="return confirm('Force deploy karo? gobazzar-git se site root me rsync hoga.')"
   class="btn btn-gray" style="margin-bottom:20px">🔄 Force Redeploy (rsync only)</a>
<?php endif; ?>

<!-- RECENT COMMITS -->
<div class="card">
  <div class="card-head"><span class="icon">📝</span><h3>Recent Commits (GitHub)</h3></div>
  <div class="card-body">
    <?php foreach (array_slice($commitsData, 0, 5) as $commit):
      $hash = substr($commit['sha'] ?? '', 0, 7);
      $msg  = $commit['commit']['message'] ?? '';
      $auth = $commit['commit']['author']['name'] ?? '';
      $time = $commit['commit']['author']['date'] ?? '';
      $timeH = $time ? date('d M H:i', strtotime($time)) : '';
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
      <div class="commit-meta"><?= htmlspecialchars($auth) ?> · <?= htmlspecialchars($timeH) ?></div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($commitsData)): ?>
    <div style="color:#64748b;font-size:13px">GitHub API se commits load nahi hue. Repo name check karo: <code><?= $githubRepo ?></code></div>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>

</div>
</body>
</html>
