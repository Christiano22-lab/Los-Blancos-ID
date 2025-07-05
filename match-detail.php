<?php
$page_title = "Match Details";
$current_page = "matches";

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get and validate match ID
$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Handle admin form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    if (isset($_POST['update_stats'])) {
        // Update match statistics
        $stats = [
            'home_shots' => (int)$_POST['home_shots'],
            'away_shots' => (int)$_POST['away_shots'],
            'home_shots_on_target' => (int)$_POST['home_shots_on_target'],
            'away_shots_on_target' => (int)$_POST['away_shots_on_target'],
            'home_possession' => (int)$_POST['home_possession'],
            'away_possession' => (int)$_POST['away_possession'],
            'home_passes' => (int)$_POST['home_passes'],
            'away_passes' => (int)$_POST['away_passes'],
            'home_pass_accuracy' => (int)$_POST['home_pass_accuracy'],
            'away_pass_accuracy' => (int)$_POST['away_pass_accuracy'],
            'home_fouls' => (int)$_POST['home_fouls'],
            'away_fouls' => (int)$_POST['away_fouls'],
            'home_yellow_cards' => (int)$_POST['home_yellow_cards'],
            'away_yellow_cards' => (int)$_POST['away_yellow_cards'],
            'home_red_cards' => (int)$_POST['home_red_cards'],
            'away_red_cards' => (int)$_POST['away_red_cards'],
            'home_offsides' => (int)$_POST['home_offsides'],
            'away_offsides' => (int)$_POST['away_offsides'],
            'home_corners' => (int)$_POST['home_corners'],
            'away_corners' => (int)$_POST['away_corners']
        ];
        
        // Check if stats exist for this match
        $check_query = "SELECT id FROM match_statistics WHERE match_id = $match_id LIMIT 1";
        $check_result = db_query($check_query);
        
        if (db_num_rows($check_result) > 0) {
            // Update existing stats
            $update_parts = [];
            foreach ($stats as $key => $value) {
                $update_parts[] = "$key = $value";
            }
            $update_query = "UPDATE match_statistics SET " . implode(', ', $update_parts) . " WHERE match_id = $match_id";
        } else {
            // Insert new stats
            $columns = array_keys($stats);
            $values = array_values($stats);
            $insert_query = "INSERT INTO match_statistics (match_id, " . implode(', ', $columns) . ") VALUES ($match_id, " . implode(', ', $values) . ")";
            $update_query = $insert_query;
        }
        
        if (db_query($update_query)) {
            $_SESSION['message'] = "Match statistics updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating match statistics.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: match-detail.php?id=$match_id");
        exit;
    }
    
    if (isset($_POST['add_player']) || isset($_POST['edit_player'])) {
        $player_name = sanitize_input($_POST['player_name']);
        $jersey_number = (int)$_POST['jersey_number'];
        $position = sanitize_input($_POST['position']);
        $team_type = sanitize_input($_POST['team_type']); // 'home' or 'away'
        $is_starter = isset($_POST['is_starter']) ? 1 : 0;
        
        $player_name_escaped = db_escape($player_name);
        $position_escaped = db_escape($position);
        $team_type_escaped = db_escape($team_type);
        
        if (isset($_POST['edit_player'])) {
            // Update existing player
            $player_id = (int)$_POST['player_id'];
            $query = "UPDATE match_lineups SET 
                      player_name = '$player_name_escaped',
                      jersey_number = $jersey_number,
                      position = '$position_escaped',
                      is_starter = $is_starter
                      WHERE id = $player_id";
            
            $success_msg = "Player updated successfully!";
        } else {
            // Add new player
            $query = "INSERT INTO match_lineups (match_id, player_name, jersey_number, position, team_type, is_starter) 
                      VALUES ($match_id, '$player_name_escaped', $jersey_number, '$position_escaped', '$team_type_escaped', $is_starter)";
            
            $success_msg = "Player added successfully!";
        }
        
        if (db_query($query)) {
            $_SESSION['message'] = $success_msg;
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error saving player.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: match-detail.php?id=$match_id");
        exit;
    }
    
    if (isset($_POST['delete_player'])) {
        $player_id = (int)$_POST['player_id'];
        $query = "DELETE FROM match_lineups WHERE id = $player_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "Player deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting player.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: match-detail.php?id=$match_id");
        exit;
    }
}

// Get match data
$match_query = "SELECT * FROM matches WHERE id = $match_id LIMIT 1";
$match_result = db_query($match_query);

if (db_num_rows($match_result) == 0) {
    // Match not found, redirect to matches page
    header("Location: matches.php");
    exit;
}

$match = db_fetch_array($match_result);

// Get match statistics
$stats_query = "SELECT * FROM match_statistics WHERE match_id = $match_id LIMIT 1";
$stats_result = db_query($stats_query);
$match_stats = db_num_rows($stats_result) > 0 ? db_fetch_array($stats_result) : null;

// Default stats if none exist
if (!$match_stats) {
    $match_stats = [
        'home_shots' => 0, 'away_shots' => 0,
        'home_shots_on_target' => 0, 'away_shots_on_target' => 0,
        'home_possession' => 50, 'away_possession' => 50,
        'home_passes' => 0, 'away_passes' => 0,
        'home_pass_accuracy' => 0, 'away_pass_accuracy' => 0,
        'home_fouls' => 0, 'away_fouls' => 0,
        'home_yellow_cards' => 0, 'away_yellow_cards' => 0,
        'home_red_cards' => 0, 'away_red_cards' => 0,
        'home_offsides' => 0, 'away_offsides' => 0,
        'home_corners' => 0, 'away_corners' => 0
    ];
}

// Get lineups - separate starters and substitutes
$home_starters_query = "SELECT * FROM match_lineups WHERE match_id = $match_id AND team_type = 'home' AND is_starter = 1 ORDER BY jersey_number LIMIT 11";
$home_starters_result = db_query($home_starters_query);
$home_starters = db_fetch_all($home_starters_result);

$home_subs_query = "SELECT * FROM match_lineups WHERE match_id = $match_id AND team_type = 'home' AND is_starter = 0 ORDER BY jersey_number";
$home_subs_result = db_query($home_subs_query);
$home_subs = db_fetch_all($home_subs_result);

$away_starters_query = "SELECT * FROM match_lineups WHERE match_id = $match_id AND team_type = 'away' AND is_starter = 1 ORDER BY jersey_number LIMIT 11";
$away_starters_result = db_query($away_starters_query);
$away_starters = db_fetch_all($away_starters_result);

$away_subs_query = "SELECT * FROM match_lineups WHERE match_id = $match_id AND team_type = 'away' AND is_starter = 0 ORDER BY jersey_number";
$away_subs_result = db_query($away_subs_query);
$away_subs = db_fetch_all($away_subs_result);

// Get player for editing if edit_player_id is provided
$edit_player = null;
if (isset($_GET['edit_player']) && is_admin()) {
    $edit_player_id = (int)$_GET['edit_player'];
    $edit_player_query = "SELECT * FROM match_lineups WHERE id = $edit_player_id LIMIT 1";
    $edit_player_result = db_query($edit_player_query);
    if (db_num_rows($edit_player_result) == 1) {
        $edit_player = db_fetch_array($edit_player_result);
    }
}

include 'includes/header.php';
?>

<div class="match-detail-container">
    <?php display_message(); ?>
    
    <!-- Match Header -->
    <div class="match-header-section">
        <div class="match-result">
            <div class="team-section home-team">
                <div class="team-logo">
                <img src="assets/images/teams/<?php echo $match['home_team_logo']; ?>" alt="<?php echo $match['home_team']; ?>">
                </div>
                <h2 class="team-name"><?php echo $match['home_team']; ?></h2>
            </div>
            
            <div class="score-section">
                <?php if ($match['status'] == 'finished'): ?>
                    <div class="final-score">
                        <span class="score"><?php echo $match['home_score']; ?></span>
                        <span class="separator">-</span>
                        <span class="score"><?php echo $match['away_score']; ?></span>
                    </div>
                    <div class="match-status">FT</div>
                <?php else: ?>
                    <div class="match-time">
                        <?php echo date('H:i', strtotime($match['match_time'])); ?>
                    </div>
                    <div class="vs-text">VS</div>
                <?php endif; ?>
            </div>
            
            <div class="team-section away-team">
                <div class="team-logo">
                <img src="assets/images/teams/<?php echo $match['away_team_logo']; ?>" alt="<?php echo $match['away_team']; ?>">
                </div>
                <h2 class="team-name"><?php echo $match['away_team']; ?></h2>
            </div>
        </div>
        
        <div class="match-info">
            <div class="info-item">
                <i class="fas fa-trophy"></i>
                <span><?php echo $match['competition']; ?></span>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar"></i>
                <span><?php echo date('F j, Y', strtotime($match['match_date'])); ?></span>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo $match['stadium']; ?></span>
            </div>
        </div>
    </div>

    <!-- Admin Controls -->
    <?php if (is_admin()): ?>
        <div class="admin-section">
            <div class="admin-controls">
                <button class="btn btn-primary" onclick="toggleStatsForm()">
                    <i class="fas fa-chart-bar"></i> Edit Statistics
                </button>
                <button class="btn btn-secondary" onclick="togglePlayerForm()">
                    <i class="fas fa-user-plus"></i> <?php echo $edit_player ? 'Edit Player' : 'Add Player'; ?>
                </button>
                <?php if ($edit_player): ?>
                    <a href="match-detail.php?id=<?php echo $match_id; ?>" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel Edit
                    </a>
                <?php endif; ?>
            </div>

            <!-- Statistics Form -->
            <div id="statsForm" class="admin-form" style="display: none;">
                <div class="form-container">
                    <h3><i class="fas fa-chart-bar"></i> Edit Match Statistics</h3>
                    <form method="POST">
                        <div class="stats-form-grid">
                            <div class="stat-group">
                                <h4><?php echo $match['home_team']; ?></h4>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Shots</label>
                                        <input type="number" name="home_shots" value="<?php echo $match_stats['home_shots']; ?>" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Shots on Target</label>
                                        <input type="number" name="home_shots_on_target" value="<?php echo $match_stats['home_shots_on_target']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Possession (%)</label>
                                        <input type="number" name="home_possession" value="<?php echo $match_stats['home_possession']; ?>" min="0" max="100">
                                    </div>
                                    <div class="form-group">
                                        <label>Passes</label>
                                        <input type="number" name="home_passes" value="<?php echo $match_stats['home_passes']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Pass Accuracy (%)</label>
                                        <input type="number" name="home_pass_accuracy" value="<?php echo $match_stats['home_pass_accuracy']; ?>" min="0" max="100">
                                    </div>
                                    <div class="form-group">
                                        <label>Fouls</label>
                                        <input type="number" name="home_fouls" value="<?php echo $match_stats['home_fouls']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Yellow Cards</label>
                                        <input type="number" name="home_yellow_cards" value="<?php echo $match_stats['home_yellow_cards']; ?>" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Red Cards</label>
                                        <input type="number" name="home_red_cards" value="<?php echo $match_stats['home_red_cards']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Offsides</label>
                                        <input type="number" name="home_offsides" value="<?php echo $match_stats['home_offsides']; ?>" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Corners</label>
                                        <input type="number" name="home_corners" value="<?php echo $match_stats['home_corners']; ?>" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="stat-group">
                                <h4><?php echo $match['away_team']; ?></h4>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Shots</label>
                                        <input type="number" name="away_shots" value="<?php echo $match_stats['away_shots']; ?>" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Shots on Target</label>
                                        <input type="number" name="away_shots_on_target" value="<?php echo $match_stats['away_shots_on_target']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Possession (%)</label>
                                        <input type="number" name="away_possession" value="<?php echo $match_stats['away_possession']; ?>" min="0" max="100">
                                    </div>
                                    <div class="form-group">
                                        <label>Passes</label>
                                        <input type="number" name="away_passes" value="<?php echo $match_stats['away_passes']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Pass Accuracy (%)</label>
                                        <input type="number" name="away_pass_accuracy" value="<?php echo $match_stats['away_pass_accuracy']; ?>" min="0" max="100">
                                    </div>
                                    <div class="form-group">
                                        <label>Fouls</label>
                                        <input type="number" name="away_fouls" value="<?php echo $match_stats['away_fouls']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Yellow Cards</label>
                                        <input type="number" name="away_yellow_cards" value="<?php echo $match_stats['away_yellow_cards']; ?>" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Red Cards</label>
                                        <input type="number" name="away_red_cards" value="<?php echo $match_stats['away_red_cards']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Offsides</label>
                                        <input type="number" name="away_offsides" value="<?php echo $match_stats['away_offsides']; ?>" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Corners</label>
                                        <input type="number" name="away_corners" value="<?php echo $match_stats['away_corners']; ?>" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="update_stats" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Statistics
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleStatsForm()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Player Form -->
            <div id="playerForm" class="admin-form" style="display: <?php echo $edit_player ? 'block' : 'none'; ?>;">
                <div class="form-container">
                    <h3><i class="fas fa-user-plus"></i> <?php echo $edit_player ? 'Edit Player' : 'Add Player'; ?></h3>
                    <form method="POST">
                        <?php if ($edit_player): ?>
                            <input type="hidden" name="player_id" value="<?php echo $edit_player['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="player_name">Player Name *</label>
                                <input type="text" name="player_name" id="player_name" required value="<?php echo $edit_player ? htmlspecialchars($edit_player['player_name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="jersey_number">Jersey Number *</label>
                                <input type="number" name="jersey_number" id="jersey_number" required min="1" max="99" value="<?php echo $edit_player ? $edit_player['jersey_number'] : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="position">Position *</label>
                                <select name="position" id="position" required>
                                    <option value="">Select Position</option>
                                    <option value="Goalkeeper" <?php echo ($edit_player && $edit_player['position'] == 'Goalkeeper') ? 'selected' : ''; ?>>Goalkeeper</option>
                                    <option value="Defender" <?php echo ($edit_player && $edit_player['position'] == 'Defender') ? 'selected' : ''; ?>>Defender</option>
                                    <option value="Midfielder" <?php echo ($edit_player && $edit_player['position'] == 'Midfielder') ? 'selected' : ''; ?>>Midfielder</option>
                                    <option value="Forward" <?php echo ($edit_player && $edit_player['position'] == 'Forward') ? 'selected' : ''; ?>>Forward</option>
                                </select>
                            </div>
                            <?php if (!$edit_player): ?>
                                <div class="form-group">
                                    <label for="team_type">Team *</label>
                                    <select name="team_type" id="team_type" required>
                                        <option value="">Select Team</option>
                                        <option value="home"><?php echo $match['home_team']; ?></option>
                                        <option value="away"><?php echo $match['away_team']; ?></option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_starter" value="1" <?php echo ($edit_player && $edit_player['is_starter']) ? 'checked' : 'checked'; ?>>
                                    Starting XI Player
                                </label>
                                <small>Uncheck if this is a substitute player</small>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="<?php echo $edit_player ? 'edit_player' : 'add_player'; ?>" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_player ? 'Update Player' : 'Add Player'; ?>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="<?php echo $edit_player ? 'window.location.href=\'match-detail.php?id=' . $match_id . '\'' : 'togglePlayerForm()'; ?>">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div class="detail-tabs">
        <button class="tab-btn" data-tab="timeline">LINIMASA</button>
        <button class="tab-btn active" data-tab="lineup">SUSUNAN PEMAIN</button>
        <button class="tab-btn" data-tab="stats">STATISTIK</button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Timeline Tab -->
        <div class="tab-pane" id="timeline">
            <div class="timeline-section">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-time">0'</div>
                        <div class="timeline-event">
                            <i class="fas fa-play"></i>
                            <span>Match Started</span>
                        </div>
                    </div>
                    
                    <?php if ($match['status'] == 'finished'): ?>
                        <div class="timeline-item">
                            <div class="timeline-time">23'</div>
                            <div class="timeline-event goal">
                                <i class="fas fa-futbol"></i>
                                <span>Goal! <?php echo $match['home_team']; ?> 1-0</span>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-time">45'</div>
                            <div class="timeline-event goal">
                                <i class="fas fa-futbol"></i>
                                <span>Goal! <?php echo $match['home_team']; ?> 2-0</span>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-time">67'</div>
                            <div class="timeline-event goal">
                                <i class="fas fa-futbol"></i>
                                <span>Goal! <?php echo $match['away_team']; ?> 2-1</span>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-time">89'</div>
                            <div class="timeline-event goal">
                                <i class="fas fa-futbol"></i>
                                <span>Goal! <?php echo $match['home_team']; ?> <?php echo $match['home_score']; ?>-<?php echo $match['away_score']; ?></span>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-time">90'</div>
                            <div class="timeline-event">
                                <i class="fas fa-flag-checkered"></i>
                                <span>Full Time</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-time">--</div>
                            <div class="timeline-event">
                                <i class="fas fa-clock"></i>
                                <span>Match events will appear here</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Lineup Tab -->
        <div class="tab-pane active" id="lineup">
            <div class="lineup-section">
                <div class="lineup-container">
                    <div class="team-lineup">
                        <div class="team-header">
                        <img src="assets/images/teams/<?php echo $match['home_team_logo']; ?>" alt="<?php echo $match['home_team']; ?>">
                            <h3><?php echo $match['home_team']; ?></h3>
                        </div>
                        
                        <div class="players-list">
                            <h4>Starting XI</h4>
                            <?php if (!empty($home_starters)): ?>
                                <?php foreach ($home_starters as $player): ?>
                                    <div class="player-item">
                                        <?php if (is_admin()): ?>
                                            <div class="player-actions">
                                                <a href="match-detail.php?id=<?php echo $match_id; ?>&edit_player=<?php echo $player['id']; ?>" class="btn-edit-small" title="Edit Player">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this player?');">
                                                    <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                                                    <button type="submit" name="delete_player" class="btn-delete-small" title="Delete Player">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        <span class="jersey-number"><?php echo $player['jersey_number']; ?></span>
                                        <span class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></span>
                                        <span class="player-position"><?php echo $player['position']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-players">
                                    <p>No starting players added yet</p>
                                    <?php if (is_admin()): ?>
                                        <button class="btn btn-sm btn-primary" onclick="togglePlayerForm()">
                                            <i class="fas fa-plus"></i> Add Players
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (count($home_starters) < 11 && is_admin()): ?>
                                <div class="lineup-info">
                                    <small><i class="fas fa-info-circle"></i> Need <?php echo 11 - count($home_starters); ?> more starting players</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($home_subs)): ?>
                            <div class="players-list">
                                <h4>Substitutes</h4>
                                <?php foreach ($home_subs as $player): ?>
                                    <div class="player-item substitute">
                                        <?php if (is_admin()): ?>
                                            <div class="player-actions">
                                                <a href="match-detail.php?id=<?php echo $match_id; ?>&edit_player=<?php echo $player['id']; ?>" class="btn-edit-small" title="Edit Player">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this player?');">
                                                    <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                                                    <button type="submit" name="delete_player" class="btn-delete-small" title="Delete Player">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        <span class="jersey-number"><?php echo $player['jersey_number']; ?></span>
                                        <span class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></span>
                                        <span class="player-position"><?php echo $player['position']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="team-lineup">
                        <div class="team-header">
                        <img src="assets/images/teams/<?php echo $match['away_team_logo']; ?>" alt="<?php echo $match['away_team']; ?>">
                            <h3><?php echo $match['away_team']; ?></h3>
                        </div>
                        
                        <div class="players-list">
                            <h4>Starting XI</h4>
                            <?php if (!empty($away_starters)): ?>
                                <?php foreach ($away_starters as $player): ?>
                                    <div class="player-item">
                                        <?php if (is_admin()): ?>
                                            <div class="player-actions">
                                                <a href="match-detail.php?id=<?php echo $match_id; ?>&edit_player=<?php echo $player['id']; ?>" class="btn-edit-small" title="Edit Player">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this player?');">
                                                    <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                                                    <button type="submit" name="delete_player" class="btn-delete-small" title="Delete Player">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        <span class="jersey-number"><?php echo $player['jersey_number']; ?></span>
                                        <span class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></span>
                                        <span class="player-position"><?php echo $player['position']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-players">
                                    <p>No starting players added yet</p>
                                    <?php if (is_admin()): ?>
                                        <button class="btn btn-sm btn-primary" onclick="togglePlayerForm()">
                                            <i class="fas fa-plus"></i> Add Players
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (count($away_starters) < 11 && is_admin()): ?>
                                <div class="lineup-info">
                                    <small><i class="fas fa-info-circle"></i> Need <?php echo 11 - count($away_starters); ?> more starting players</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($away_subs)): ?>
                            <div class="players-list">
                                <h4>Substitutes</h4>
                                <?php foreach ($away_subs as $player): ?>
                                    <div class="player-item substitute">
                                        <?php if (is_admin()): ?>
                                            <div class="player-actions">
                                                <a href="match-detail.php?id=<?php echo $match_id; ?>&edit_player=<?php echo $player['id']; ?>" class="btn-edit-small" title="Edit Player">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this player?');">
                                                    <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                                                    <button type="submit" name="delete_player" class="btn-delete-small" title="Delete Player">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        <span class="jersey-number"><?php echo $player['jersey_number']; ?></span>
                                        <span class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></span>
                                        <span class="player-position"><?php echo $player['position']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="manager-section">
                    <h4>MANAJER</h4>
                    <div class="manager-info">
                        <div class="manager-item">
                            <span class="manager-name">Carlo Ancelotti</span>
                        </div>
                        <div class="manager-item">
                            <span class="manager-name">Opponent Manager</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Tab -->
        <div class="tab-pane" id="stats">
            <div class="stats-section">
                <h3>STATISTIK TIM</h3>
                
                <div class="stats-container">
                    <div class="team-logos">
                        <img src="assets/images/teams/<?php echo $match['home_team_logo']; ?>" alt="<?php echo $match['home_team']; ?>">
                        <img src="assets/images/teams/<?php echo $match['away_team_logo']; ?>" alt="<?php echo $match['away_team']; ?>">
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_shots']; ?></div>
                            <div class="stat-label">Tembakan</div>
                            <div class="stat-value away"><?php echo $match_stats['away_shots']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home highlight"><?php echo $match_stats['home_shots_on_target']; ?></div>
                            <div class="stat-label">Tembakan ke arah gawang</div>
                            <div class="stat-value away highlight"><?php echo $match_stats['away_shots_on_target']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_possession']; ?>%</div>
                            <div class="stat-label">Penguasaan bola</div>
                            <div class="stat-value away highlight"><?php echo $match_stats['away_possession']; ?>%</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_passes']; ?></div>
                            <div class="stat-label">Operan</div>
                            <div class="stat-value away highlight"><?php echo $match_stats['away_passes']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_pass_accuracy']; ?>%</div>
                            <div class="stat-label">Akurasi operan</div>
                            <div class="stat-value away highlight"><?php echo $match_stats['away_pass_accuracy']; ?>%</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_fouls']; ?></div>
                            <div class="stat-label">Pelanggaran</div>
                            <div class="stat-value away highlight"><?php echo $match_stats['away_fouls']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_yellow_cards']; ?></div>
                            <div class="stat-label">Kartu kuning</div>
                            <div class="stat-value away"><?php echo $match_stats['away_yellow_cards']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home"><?php echo $match_stats['home_red_cards']; ?></div>
                            <div class="stat-label">Kartu merah</div>
                            <div class="stat-value away"><?php echo $match_stats['away_red_cards']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home highlight"><?php echo $match_stats['home_offsides']; ?></div>
                            <div class="stat-label">Offside</div>
                            <div class="stat-value away"><?php echo $match_stats['away_offsides']; ?></div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-value home highlight"><?php echo $match_stats['home_corners']; ?></div>
                            <div class="stat-label">Tendangan sudut</div>
                            <div class="stat-value away"><?php echo $match_stats['away_corners']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="back-section">
        <a href="matches.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Matches
        </a>
    </div>
</div>

<link rel="stylesheet" href="assets/css/match-detail.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Update active tab button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Update active tab pane
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
        });
    });
});

// Admin form functions
function toggleStatsForm() {
    const form = document.getElementById('statsForm');
    const playerForm = document.getElementById('playerForm');
    
    // Hide player form if open
    if (playerForm) playerForm.style.display = 'none';
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}

function togglePlayerForm() {
    const form = document.getElementById('playerForm');
    const statsForm = document.getElementById('statsForm');
    
    // Hide stats form if open
    if (statsForm) statsForm.style.display = 'none';
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
