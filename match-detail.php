<?php
// match-detail.php

// Include files in correct order
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Set current page for navigation
$current_page = 'matches';

// Debug: Check if $pdo exists
if (!isset($pdo)) {
    die("Database connection not established. Please check your database configuration.");
}

// Get and validate match ID
$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!validateMatchId($match_id)) {
    header('Location: matches.php');
    exit();
}

// Fetch match details
$match = getMatchById($pdo, $match_id);

if (!$match) {
    header('Location: matches.php?error=match_not_found');
    exit();
}

// Set default values for missing fields
$match_defaults = [
    'title' => $match['home_team'] . ' vs ' . $match['away_team'],
    'map_name' => 'TBD',
    'game_mode' => 'Standard',
    'duration' => 0,
    'tournament' => 'Regular Season',
    'description' => '',
    'home_team_logo' => 'default-team.png',
    'away_team_logo' => 'default-team.png',
    'home_score' => 0,
    'away_score' => 0,
    'status' => 'scheduled'
];

// Merge with defaults
$match = array_merge($match_defaults, $match);

// Fetch match statistics
$stats = getMatchStats($pdo, $match_id);

// Get additional match info
$winner = getMatchWinner($match);

include_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/match-detail.css">

<div class="match-detail-container">
    <div class="match-header">
        <div class="match-info">
            <h1 class="match-title"><?php echo sanitizeOutput($match['title']); ?></h1>
            <div class="match-meta">
                <span class="match-date"><?php echo formatMatchDate($match['match_date']); ?></span>
                <span class="match-time"><?php echo formatMatchTime($match['match_time']); ?></span>
                <span class="match-status <?php echo getMatchStatusClass($match['status']); ?>">
                    <?php echo ucfirst($match['status']); ?>
                </span>
                <?php if ($winner): ?>
                <span class="match-winner">Winner: <?php echo sanitizeOutput($winner); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="match-content">
        <div class="teams-section">
            <div class="team team-home">
                <div class="team-logo">
                    <img src="<?php echo getTeamLogoPath($match['home_team_logo']); ?>" 
                         alt="<?php echo sanitizeOutput($match['home_team']); ?>">
                </div>
                <h2 class="team-name"><?php echo sanitizeOutput($match['home_team']); ?></h2>
                <div class="team-score"><?php echo $match['home_score']; ?></div>
            </div>

            <div class="vs-divider">
                <span>VS</span>
            </div>

            <div class="team team-away">
                <div class="team-logo">
                    <img src="<?php echo getTeamLogoPath($match['away_team_logo']); ?>" 
                         alt="<?php echo sanitizeOutput($match['away_team']); ?>">
                </div>
                <h2 class="team-name"><?php echo sanitizeOutput($match['away_team']); ?></h2>
                <div class="team-score"><?php echo $match['away_score']; ?></div>
            </div>
        </div>

        <div class="match-details">
            <div class="detail-section">
                <h3>Match Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Map:</span>
                        <span class="value"><?php echo sanitizeOutput($match['map_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Game Mode:</span>
                        <span class="value"><?php echo sanitizeOutput($match['game_mode']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Duration:</span>
                        <span class="value"><?php echo formatDuration($match['duration']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Tournament:</span>
                        <span class="value"><?php echo sanitizeOutput($match['tournament']); ?></span>
                    </div>
                </div>
            </div>

            <?php if (!empty($stats)): ?>
            <div class="stats-section">
                <h3>Match Statistics</h3>
                <div class="stats-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Team</th>
                                <th>Kills</th>
                                <th>Deaths</th>
                                <th>Assists</th>
                                <th>K/D</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats as $stat): ?>
                            <tr>
                                <td><?php echo sanitizeOutput($stat['player_name']); ?></td>
                                <td><?php echo sanitizeOutput($stat['team']); ?></td>
                                <td><?php echo $stat['kills']; ?></td>
                                <td><?php echo $stat['deaths']; ?></td>
                                <td><?php echo $stat['assists']; ?></td>
                                <td><?php echo calculateKDRatio($stat['kills'], $stat['deaths']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($match['description'])): ?>
            <div class="description-section">
                <h3>Match Summary</h3>
                <p><?php echo nl2br(sanitizeOutput($match['description'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="match-actions">
        <a href="matches.php" class="btn btn-secondary">← Back to Matches</a>
        <button class="btn btn-primary" onclick="shareMatch()">Share Match</button>
        
        <?php if (isset($_SESSION['user_role']) && canEditMatch($_SESSION['user_role'])): ?>
        <a href="admin/edit-match.php?id=<?php echo $match_id; ?>" class="btn btn-warning">Edit Match</a>
        <?php endif; ?>
    </div>
</div>

<script>
function shareMatch() {
    const matchTitle = '<?php echo addslashes(sanitizeOutput($match['title'])); ?>';
    const matchTeams = '<?php echo addslashes(sanitizeOutput($match['home_team'] . ' vs ' . $match['away_team'])); ?>';
    
    if (navigator.share) {
        navigator.share({
            title: matchTitle,
            text: 'Check out this match: ' + matchTeams,
            url: window.location.href
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Match link copied to clipboard!');
        }).catch(() => {
            const textArea = document.createElement('textarea');
            textArea.value = window.location.href;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Match link copied to clipboard!');
        });
    }
}
</script>

<?php include_once 'includes/footer.php'; ?>