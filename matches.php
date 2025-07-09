<?php
$page_title = "Matches";
$page_description = "View upcoming matches and results for Real Madrid Football Club";
$current_page = "matches";

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submission for adding/editing matches (Admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin()) {
    if (isset($_POST['add_match']) || isset($_POST['edit_match'])) {
        $competition = sanitize_input($_POST['competition']);
        $home_team = sanitize_input($_POST['home_team']);
        $away_team = sanitize_input($_POST['away_team']);
        $match_date = sanitize_input($_POST['match_date']);
        $match_time = sanitize_input($_POST['match_time']);
        $stadium = sanitize_input($_POST['stadium']);
        $home_score = !empty($_POST['home_score']) ? (int)$_POST['home_score'] : null;
        $away_score = !empty($_POST['away_score']) ? (int)$_POST['away_score'] : null;
        $status = sanitize_input($_POST['status']);
        
        // Handle logo uploads - fix path handling
        $home_team_logo = isset($_POST['current_home_logo']) ? $_POST['current_home_logo'] : 'logo.png';
        $away_team_logo = isset($_POST['current_away_logo']) ? $_POST['current_away_logo'] : 'logo.png';
        
        if (isset($_FILES['home_team_logo']) && $_FILES['home_team_logo']['error'] === 0) {
            $upload_dir = 'assets/images/teams/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['home_team_logo']['name'], PATHINFO_EXTENSION);
            $filename = 'home_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['home_team_logo']['tmp_name'], $upload_path)) {
                $home_team_logo = $filename; // Store only filename, not full path
            }
        }
        
        if (isset($_FILES['away_team_logo']) && $_FILES['away_team_logo']['error'] === 0) {
            $upload_dir = 'assets/images/teams/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['away_team_logo']['name'], PATHINFO_EXTENSION);
            $filename = 'away_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['away_team_logo']['tmp_name'], $upload_path)) {
                $away_team_logo = $filename; // Store only filename, not full path
            }
        }
        
        // Prepare data for update/insert
        $match_data = [
            'competition' => $competition,
            'home_team' => $home_team,
            'away_team' => $away_team,
            'home_team_logo' => $home_team_logo,
            'away_team_logo' => $away_team_logo,
            'match_date' => $match_date,
            'match_time' => $match_time,
            'stadium' => $stadium,
            'home_score' => $home_score,
            'away_score' => $away_score,
            'status' => $status
        ];
        
        if (isset($_POST['edit_match'])) {
            // Update existing match
            $match_id = (int)$_POST['match_id'];
            
            if (update_match($match_id, $match_data)) {
                $_SESSION['message'] = "Match updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error updating match.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            // Insert new match
            if (insert_match($match_data)) {
                $_SESSION['message'] = "Match added successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error adding match.";
                $_SESSION['message_type'] = "error";
            }
        }
        
        header("Location: matches.php");
        exit;
    }
    
    // Handle delete match
    if (isset($_POST['delete_match'])) {
        $match_id = (int)$_POST['match_id'];
        $query = "DELETE FROM matches WHERE id = $match_id";
        
        if (db_query($query)) {
            $_SESSION['message'] = "Match deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting match.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: matches.php");
        exit;
    }
}

// Get match for editing if edit_id is provided
$edit_match = null;
if (isset($_GET['edit']) && is_admin()) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = "SELECT * FROM matches WHERE id = $edit_id LIMIT 1";
    $edit_result = db_query($edit_query);
    if (db_num_rows($edit_result) == 1) {
        $edit_match = db_fetch_array($edit_result);
    }
}

// Get matches data
create_dummy_matches_data();

$upcoming_matches_query = "SELECT * FROM matches WHERE match_date >= CURDATE() AND status IN ('scheduled', 'live') ORDER BY match_date ASC";
$upcoming_matches_result = db_query($upcoming_matches_query);
$upcoming_matches = db_fetch_all($upcoming_matches_result);

$recent_results_query = "SELECT * FROM matches WHERE status = 'finished' ORDER BY match_date DESC LIMIT 10";
$recent_results_result = db_query($recent_results_query);
$recent_results = db_fetch_all($recent_results_result);

$competitions = [
    ['competition' => 'La Liga'],
    ['competition' => 'Champions League'],
    ['competition' => 'Copa del Rey'],
    ['competition' => 'UEFA Super Cup'],
    ['competition' => 'Club World Cup']
];

include 'includes/header.php';
?>

<div class="matches-container">
    <?php display_message(); ?>
    
    <section class="matches-hero" style="background-image: linear-gradient(rgba(0, 38, 96, 0.8), rgba(0, 38, 96, 0.6)), url('assets/images/1.jpeg');">
        <div class="hero-content">
            <h1>Matches</h1>
            <p>Follow Real Madrid's journey through every competition</p>
        </div>
        <div class="hero-bg"></div>
    </section>

    <!-- Admin Add/Edit Match Section -->
    <?php if (is_admin()): ?>
        <div class="admin-section">
            <div class="admin-controls">
                <?php if (!$edit_match): ?>
                    <button class="btn btn-primary" onclick="toggleAddMatchForm()">
                        <i class="fas fa-plus"></i> Add New Match
                    </button>
                <?php else: ?>
                    <div class="edit-mode-header">
                        <h3><i class="fas fa-edit"></i> Editing Match: <?php echo htmlspecialchars($edit_match['home_team'] . ' vs ' . $edit_match['away_team']); ?></h3>
                        <a href="matches.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel Edit
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add/Edit Match Form -->
            <div id="addMatchForm" class="add-match-form" style="display: <?php echo $edit_match ? 'block' : 'none'; ?>;">
                <div class="form-container">
                    <h3>
                        <i class="fas fa-futbol"></i> 
                        <?php echo $edit_match ? 'Edit Match' : 'Add New Match'; ?>
                    </h3>
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($edit_match): ?>
                            <input type="hidden" name="match_id" value="<?php echo $edit_match['id']; ?>">
                            <input type="hidden" name="current_home_logo" value="<?php echo htmlspecialchars($edit_match['home_team_logo']); ?>">
                            <input type="hidden" name="current_away_logo" value="<?php echo htmlspecialchars($edit_match['away_team_logo']); ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="competition">Competition *</label>
                                <select name="competition" id="competition" required>
                                    <option value="">Select Competition</option>
                                    <option value="La Liga" <?php echo ($edit_match && $edit_match['competition'] == 'La Liga') ? 'selected' : ''; ?>>La Liga</option>
                                    <option value="Champions League" <?php echo ($edit_match && $edit_match['competition'] == 'Champions League') ? 'selected' : ''; ?>>Champions League</option>
                                    <option value="Copa del Rey" <?php echo ($edit_match && $edit_match['competition'] == 'Copa del Rey') ? 'selected' : ''; ?>>Copa del Rey</option>
                                    <option value="UEFA Super Cup" <?php echo ($edit_match && $edit_match['competition'] == 'UEFA Super Cup') ? 'selected' : ''; ?>>UEFA Super Cup</option>
                                    <option value="Club World Cup" <?php echo ($edit_match && $edit_match['competition'] == 'Club World Cup') ? 'selected' : ''; ?>>Club World Cup</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select name="status" id="status" required onchange="toggleScoreFields()">
                                    <option value="scheduled" <?php echo ($edit_match && $edit_match['status'] == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="finished" <?php echo ($edit_match && $edit_match['status'] == 'finished') ? 'selected' : ''; ?>>Finished</option>
                                    <option value="live" <?php echo ($edit_match && $edit_match['status'] == 'live') ? 'selected' : ''; ?>>Live</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="home_team">Home Team *</label>
                                <input type="text" name="home_team" id="home_team" required value="<?php echo $edit_match ? htmlspecialchars($edit_match['home_team']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="away_team">Away Team *</label>
                                <input type="text" name="away_team" id="away_team" required value="<?php echo $edit_match ? htmlspecialchars($edit_match['away_team']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="home_team_logo">Home Team Logo</label>
                                <?php if ($edit_match && $edit_match['home_team_logo']): ?>
                                    <div class="current-logo">
                                        <img src="<?php echo get_team_logo_path($edit_match['home_team_logo']); ?>" alt="Current Home Logo" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 0.5rem;">
                                        <small>Current logo (leave empty to keep)</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="home_team_logo" id="home_team_logo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="away_team_logo">Away Team Logo</label>
                                <?php if ($edit_match && $edit_match['away_team_logo']): ?>
                                    <div class="current-logo">
                                        <img src="<?php echo get_team_logo_path($edit_match['away_team_logo']); ?>" alt="Current Away Logo" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 0.5rem;">
                                        <small>Current logo (leave empty to keep)</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="away_team_logo" id="away_team_logo" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="match_date">Match Date *</label>
                                <input type="date" name="match_date" id="match_date" required value="<?php echo $edit_match ? $edit_match['match_date'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="match_time">Match Time *</label>
                                <input type="time" name="match_time" id="match_time" required value="<?php echo $edit_match ? $edit_match['match_time'] : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="stadium">Stadium *</label>
                            <input type="text" name="stadium" id="stadium" required placeholder="e.g., Santiago Bernabéu" value="<?php echo $edit_match ? htmlspecialchars($edit_match['stadium']) : ''; ?>">
                        </div>
                        
                        <div class="form-row score-fields" id="scoreFields" style="display: <?php echo ($edit_match && $edit_match['status'] == 'finished') ? 'flex' : 'none'; ?>;">
                            <div class="form-group">
                                <label for="home_score">Home Score</label>
                                <input type="number" name="home_score" id="home_score" min="0" value="<?php echo $edit_match ? $edit_match['home_score'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="away_score">Away Score</label>
                                <input type="number" name="away_score" id="away_score" min="0" value="<?php echo $edit_match ? $edit_match['away_score'] : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="<?php echo $edit_match ? 'edit_match' : 'add_match'; ?>" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_match ? 'Update Match' : 'Add Match'; ?>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="<?php echo $edit_match ? 'window.location.href=\'matches.php\'' : 'toggleAddMatchForm()'; ?>">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="matches-content">
        <div class="filter-section">
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="competition-filter">Competition:</label>
                    <select id="competition-filter" class="filter-select">
                        <option value="all">All Competitions</option>
                        <?php foreach ($competitions as $competition): ?>
                            <option value="<?php echo strtolower(str_replace(' ', '-', $competition['competition'])); ?>">
                                <?php echo $competition['competition']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="matches-tabs">
            <div class="tab-header">
                <button class="tab-button active" data-tab="upcoming">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Upcoming Matches</span>
                </button>
                <button class="tab-button" data-tab="results">
                    <i class="fas fa-history"></i>
                    <span>Recent Results</span>
                </button>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="upcoming">
                    <div class="matches-grid">
                        <?php if (empty($upcoming_matches)): ?>
                            <div class="no-matches">
                                <i class="far fa-calendar-times"></i>
                                <h3>No Upcoming Matches</h3>
                                <p>Check back soon for the latest fixtures</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($upcoming_matches as $match): ?>
                                <div class="match-card upcoming" data-competition="<?php echo strtolower(str_replace(' ', '-', $match['competition'])); ?>">
                                    <?php if (is_admin()): ?>
                                        <div class="admin-actions">
                                            <a href="matches.php?edit=<?php echo $match['id']; ?>" class="btn-edit" title="Edit Match">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this match?');">
                                                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                                <button type="submit" name="delete_match" class="btn-delete" title="Delete Match">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="match-header">
                                        <div class="competition-badge">
                                            <i class="fas fa-trophy"></i>
                                            <span><?php echo $match['competition']; ?></span>
                                        </div>
                                        <div class="match-date">
                                            <?php echo date('M j, Y', strtotime($match['match_date'])); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="match-teams">
                                        <div class="team home-team">
                                            <div class="team-logo">
                                                <img src="<?php echo get_team_logo_path($match['home_team_logo']); ?>" alt="<?php echo htmlspecialchars($match['home_team']); ?>" onerror="this.src='/placeholder.svg?height=60&width=60'">
                                            </div>
                                            <div class="team-name"><?php echo $match['home_team']; ?></div>
                                        </div>
                                        
                                        <div class="match-info">
                                            <div class="match-time">
                                                <?php echo date('H:i', strtotime($match['match_time'])); ?>
                                            </div>
                                            <div class="vs-text">VS</div>
                                        </div>
                                        
                                        <div class="team away-team">
                                            <div class="team-logo">
                                                <img src="<?php echo get_team_logo_path($match['away_team_logo']); ?>" alt="<?php echo htmlspecialchars($match['away_team']); ?>" onerror="this.src='/placeholder.svg?height=60&width=60'">
                                            </div>
                                            <div class="team-name"><?php echo $match['away_team']; ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="match-venue">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo $match['stadium']; ?></span>
                                    </div>
                                    
                                    <div class="match-actions">
                                        <a href="match-detail.php?id=<?php echo $match['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-info-circle"></i>
                                            Match Preview
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="tab-pane" id="results">
                    <div class="matches-grid">
                        <?php if (empty($recent_results)): ?>
                            <div class="no-matches">
                                <i class="far fa-calendar-times"></i>
                                <h3>No Recent Results</h3>
                                <p>Recent match results will appear here</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_results as $match): ?>
                                <div class="match-card result" data-competition="<?php echo strtolower(str_replace(' ', '-', $match['competition'])); ?>">
                                    <?php if (is_admin()): ?>
                                        <div class="admin-actions">
                                            <a href="matches.php?edit=<?php echo $match['id']; ?>" class="btn-edit" title="Edit Match">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this match?');">
                                                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                                <button type="submit" name="delete_match" class="btn-delete" title="Delete Match">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="match-header">
                                        <div class="competition-badge">
                                            <i class="fas fa-trophy"></i>
                                            <span><?php echo $match['competition']; ?></span>
                                        </div>
                                        <div class="match-date">
                                            <?php echo date('M j, Y', strtotime($match['match_date'])); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="match-teams">
                                        <div class="team home-team <?php echo ($match['home_score'] > $match['away_score']) ? 'winner' : (($match['home_score'] < $match['away_score']) ? 'loser' : 'draw'); ?>">
                                            <div class="team-logo">
                                                <img src="<?php echo get_team_logo_path($match['home_team_logo']); ?>" alt="<?php echo htmlspecialchars($match['home_team']); ?>" onerror="this.src='/placeholder.svg?height=60&width=60'">
                                            </div>
                                            <div class="team-name"><?php echo $match['home_team']; ?></div>
                                        </div>
                                        
                                        <div class="match-info">
                                            <div class="final-score">
                                                <span class="score home-score"><?php echo $match['home_score']; ?></span>
                                                <span class="separator">-</span>
                                                <span class="score away-score"><?php echo $match['away_score']; ?></span>
                                            </div>
                                            <div class="match-status">FT</div>
                                        </div>
                                        
                                        <div class="team away-team <?php echo ($match['away_score'] > $match['home_score']) ? 'winner' : (($match['away_score'] < $match['home_score']) ? 'loser' : 'draw'); ?>">
                                            <div class="team-logo">
                                                <img src="<?php echo get_team_logo_path($match['away_team_logo']); ?>" alt="<?php echo htmlspecialchars($match['away_team']); ?>" onerror="this.src='/placeholder.svg?height=60&width=60'">
                                            </div>
                                            <div class="team-name"><?php echo $match['away_team']; ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="match-venue">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo $match['stadium']; ?></span>
                                    </div>
                                    
                                    <div class="match-actions">
                                        <a href="match-detail.php?id=<?php echo $match['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-chart-bar"></i>
                                            Full Report
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/matches.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
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
    
    // Competition filter functionality
    const competitionFilter = document.getElementById('competition-filter');
    const matchCards = document.querySelectorAll('.match-card');
    
    competitionFilter.addEventListener('change', function() {
        const selectedCompetition = this.value;
        
        matchCards.forEach(card => {
            if (selectedCompetition === 'all' || card.dataset.competition === selectedCompetition) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Initialize score fields visibility
    toggleScoreFields();
});

function toggleAddMatchForm() {
    const form = document.getElementById('addMatchForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}

function toggleScoreFields() {
    const status = document.getElementById('status').value;
    const scoreFields = document.getElementById('scoreFields');
    
    if (status === 'finished') {
        scoreFields.style.display = 'flex';
        document.getElementById('home_score').required = true;
        document.getElementById('away_score').required = true;
    } else {
        scoreFields.style.display = 'none';
        document.getElementById('home_score').required = false;
        document.getElementById('away_score').required = false;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
