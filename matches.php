<?php
$page_title = "Matches";
$page_description = "View upcoming matches and results for Real Madrid Football Club";
$current_page = "matches";

require_once 'includes/db.php';

// Get all upcoming matches
$query = "SELECT * FROM matches WHERE match_date >= CURDATE() ORDER BY match_date ASC";
$result = db_query($query);
$upcoming_matches = db_fetch_all($result);

// Get all recent results
$query = "SELECT * FROM matches WHERE match_date < CURDATE() AND home_score IS NOT NULL ORDER BY match_date DESC";
$result = db_query($query);
$recent_results = db_fetch_all($result);

// Get competitions for filter
$query = "SELECT DISTINCT competition FROM matches ORDER BY competition";
$result = db_query($query);
$competitions = db_fetch_all($result);

include 'includes/header.php';
?>

<div class="container">
    <section class="page-header">
        <h1>Matches</h1>
        <p>View upcoming fixtures and recent results</p>
    </section>

    <section class="section">
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

        <div class="tabs">
            <div class="tab-header">
                <button class="tab-button active" data-tab="upcoming">Upcoming Matches</button>
                <button class="tab-button" data-tab="results">Recent Results</button>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="upcoming">
                    <div class="match-grid match-page-grid">
                        <?php if (empty($upcoming_matches)): ?>
                            <div class="no-matches">
                                <p>No upcoming matches scheduled at the moment.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($upcoming_matches as $match): ?>
                                <div class="match-card" data-competition="<?php echo strtolower(str_replace(' ', '-', $match['competition'])); ?>">
                                    <div class="match-header">
                                        <span class="match-competition"><?php echo $match['competition']; ?></span>
                                        <span class="match-date"><i class="far fa-calendar"></i> <?php echo format_date($match['match_date']); ?></span>
                                    </div>
                                    <div class="match-teams">
                                        <div class="team">
                                            <img src="<?php echo $match['home_team_logo']; ?>" alt="<?php echo $match['home_team']; ?>">
                                            <span><?php echo $match['home_team']; ?></span>
                                        </div>
                                        <div class="match-info">
                                            <div class="match-status">VS</div>
                                            <div class="match-time"><?php echo $match['match_time']; ?></div>
                                        </div>
                                        <div class="team">
                                            <img src="<?php echo $match['away_team_logo']; ?>" alt="<?php echo $match['away_team']; ?>">
                                            <span><?php echo $match['away_team']; ?></span>
                                        </div>
                                    </div>
                                    <div class="match-stadium"><?php echo $match['stadium']; ?></div>
                                    <div class="match-actions">
                                        <a href="match-detail.php?id=<?php echo $match['id']; ?>" class="btn btn-sm">Match Details</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="tab-pane" id="results">
                    <div class="match-grid match-page-grid">
                        <?php if (empty($recent_results)): ?>
                            <div class="no-matches">
                                <p>No recent match results available.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_results as $match): ?>
                                <div class="match-card" data-competition="<?php echo strtolower(str_replace(' ', '-', $match['competition'])); ?>">
                                    <div class="match-header">
                                        <span class="match-competition"><?php echo $match['competition']; ?></span>
                                        <span class="match-date"><i class="far fa-calendar"></i> <?php echo format_date($match['match_date']); ?></span>
                                    </div>
                                    <div class="match-teams">
                                        <div class="team">
                                            <img src="<?php echo $match['home_team_logo']; ?>" alt="<?php echo $match['home_team']; ?>">
                                            <span><?php echo $match['home_team']; ?></span>
                                        </div>
                                        <div class="match-info">
                                            <div class="match-score"><?php echo $match['home_score']; ?> - <?php echo $match['away_score']; ?></div>
                                            <div class="match-status">Completed</div>
                                        </div>
                                        <div class="team">
                                            <img src="<?php echo $match['away_team_logo']; ?>" alt="<?php echo $match['away_team']; ?>">
                                            <span><?php echo $match['away_team']; ?></span>
                                        </div>
                                    </div>
                                    <div class="match-actions">
                                        <a href="match-detail.php?id=<?php echo $match['id']; ?>" class="btn btn-sm">Match Details</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="match-calendar">
            <h2>Match Calendar</h2>
            <div class="calendar-box">
                <p>View and download the complete match schedule for the season</p>
                <a href="assets/files/real-madrid-schedule.pdf" download class="btn btn-primary">Download Calendar</a>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>

<?php include 'includes/footer.php'; ?>