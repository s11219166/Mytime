@extends('layouts.app')

@section('title', 'Inspiration Hub - MyTime')

@section('content')
<div class="inspiration-container" id="inspirationHub">
    <!-- Top Navigation & Controls -->
    <div class="inspiration-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="inspiration-title">
                        <i class="fas fa-star-half-alt me-2"></i>
                        Inspiration Hub
                    </h1>
                    <p class="inspiration-subtitle">Recharge your mind, fuel your motivation</p>
                </div>
                <div class="col-lg-6">
                    <div class="header-controls">
                        <!-- Break Timer -->
                        <div class="break-timer-widget">
                            <button class="btn-timer" onclick="showBreakTimer()">
                                <i class="fas fa-clock me-2"></i>Take a Break
                            </button>
                        </div>
                        <!-- Random Video -->
                        <button class="btn-random" onclick="playRandomVideo()">
                            <i class="fas fa-random me-2"></i>Surprise Me
                        </button>
                        <!-- Focus Mode -->
                        <button class="btn-focus" onclick="toggleFocusMode()" title="Focus Mode (F)">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="inspiration-main">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar Navigation -->
                <div class="col-lg-2 sidebar-col" id="inspirationSidebar">
                    <div class="inspiration-sidebar">
                        <div class="sidebar-section">
                            <h6 class="sidebar-heading">CATEGORIES</h6>
                            <ul class="category-nav">
                                <li class="category-item active" data-category="all" onclick="filterByCategory('all')">
                                    <i class="fas fa-th"></i> All Videos
                                </li>
                                <li class="category-item" data-category="motivational" onclick="filterByCategory('motivational')">
                                    <i class="fas fa-fire"></i> Motivational
                                </li>
                                <li class="category-item" data-category="entertainment" onclick="filterByCategory('entertainment')">
                                    <i class="fas fa-smile"></i> Entertainment
                                </li>
                            </ul>
                        </div>

                        <div class="sidebar-section">
                            <h6 class="sidebar-heading">FILTERS</h6>
                            <ul class="filter-nav">
                                <li class="filter-item active" data-filter="all" onclick="filterByType('all')">
                                    <i class="fas fa-star"></i> Most Popular
                                </li>
                                <li class="filter-item" data-filter="recent" onclick="filterByType('recent')">
                                    <i class="fas fa-clock"></i> Recently Added
                                </li>
                                <li class="filter-item" data-filter="short" onclick="filterByType('short')">
                                    <i class="fas fa-bolt"></i> Short (< 5 min)
                                </li>
                                <li class="filter-item" data-filter="long" onclick="filterByType('long')">
                                    <i class="fas fa-film"></i> Long (10+ min)
                                </li>
                            </ul>
                        </div>

                        <div class="sidebar-section">
                            <h6 class="sidebar-heading">MY LIBRARY</h6>
                            <ul class="library-nav">
                                <li class="library-item" onclick="showFavorites()">
                                    <i class="fas fa-heart"></i> Favorites <span class="count" id="favCount">0</span>
                                </li>
                                <li class="library-item" onclick="showWatchLater()">
                                    <i class="fas fa-bookmark"></i> Watch Later <span class="count" id="watchLaterCount">0</span>
                                </li>
                            </ul>
                        </div>

                        <div class="sidebar-section">
                            <h6 class="sidebar-heading">TAGS</h6>
                            <div class="tag-cloud">
                                <span class="tag-badge" onclick="filterByTag('leadership')">Leadership</span>
                                <span class="tag-badge" onclick="filterByTag('mindfulness')">Mindfulness</span>
                                <span class="tag-badge" onclick="filterByTag('fitness')">Fitness</span>
                                <span class="tag-badge" onclick="filterByTag('music')">Music</span>
                                <span class="tag-badge" onclick="filterByTag('comedy')">Comedy</span>
                                <span class="tag-badge" onclick="filterByTag('nature')">Nature</span>
                                <span class="tag-badge" onclick="filterByTag('productivity')">Productivity</span>
                                <span class="tag-badge" onclick="filterByTag('wellness')">Wellness</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-10 content-col">
                    <!-- Search Bar -->
                    <div class="search-section">
                        <div class="search-bar">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="videoSearch" class="search-input"
                                   placeholder="Search for inspiration, relaxation, or entertainment..."
                                   oninput="searchVideos(this.value)">
                            <button class="search-clear" onclick="clearSearch()" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Daily Inspiration -->
                    <div class="daily-inspiration-section" id="dailyInspiration">
                        <div class="daily-banner">
                            <div class="daily-header">
                                <i class="fas fa-sun daily-icon"></i>
                                <div>
                                    <h3 class="daily-title">Daily Inspiration</h3>
                                    <p class="daily-subtitle">Your motivation boost for today</p>
                                </div>
                            </div>
                        </div>
                        <div class="daily-video-card" id="dailyVideoCard">
                            <!-- Daily video will be loaded here -->
                        </div>
                    </div>

                    <!-- Featured Video Player -->
                    <div class="featured-video-section" id="featuredSection">
                        <div class="featured-player-container">
                            <div id="featuredPlayer"></div>
                            <div class="featured-overlay" id="featuredOverlay">
                                <div class="featured-content">
                                    <h2 class="featured-title" id="featuredTitle">Select a video to begin</h2>
                                    <p class="featured-description" id="featuredDescription">Choose from our curated collection below</p>
                                    <button class="btn-play-featured" onclick="playFeaturedVideo()">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="featured-info" id="featuredInfo" style="display: none;">
                            <div class="featured-details">
                                <h3 id="currentVideoTitle"></h3>
                                <div class="video-meta">
                                    <span class="meta-item"><i class="fas fa-eye me-1"></i><span id="currentViews">0</span> views</span>
                                    <span class="meta-item"><i class="fas fa-clock me-1"></i><span id="currentDuration">0:00</span></span>
                                </div>
                                <div class="video-actions">
                                    <button class="action-btn" id="likeBtn" onclick="toggleLike()">
                                        <i class="far fa-thumbs-up"></i> <span id="likeCount">0</span>
                                    </button>
                                    <button class="action-btn" id="favoriteBtn" onclick="toggleFavorite()">
                                        <i class="far fa-heart"></i> Favorite
                                    </button>
                                    <button class="action-btn" onclick="addToWatchLater()">
                                        <i class="far fa-bookmark"></i> Watch Later
                                    </button>
                                    <button class="action-btn" onclick="shareVideo()">
                                        <i class="fas fa-share"></i> Share
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Grid -->
                    <div class="videos-section">
                        <div class="section-header">
                            <h4 class="section-title" id="sectionTitle">All Videos</h4>
                            <div class="view-toggle">
                                <button class="view-btn active" onclick="setView('grid')">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button class="view-btn" onclick="setView('list')">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                        <div class="videos-grid" id="videosGrid">
                            <!-- Videos will be loaded here -->
                            <div class="loading-spinner">
                                <i class="fas fa-circle-notch fa-spin fa-3x"></i>
                                <p>Loading inspiring content...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Break Timer Modal -->
<div class="modal-overlay" id="breakTimerModal">
    <div class="modal-content break-timer-modal">
        <button class="modal-close" onclick="closeBreakTimer()">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="modal-title">
            <i class="fas fa-coffee me-2"></i>Take a Break
        </h3>
        <p class="modal-subtitle">Choose your break duration</p>
        <div class="timer-options">
            <div class="timer-option" onclick="selectBreakDuration(5)">
                <div class="timer-icon">5</div>
                <p>Minutes</p>
                <small>Quick refresh</small>
            </div>
            <div class="timer-option" onclick="selectBreakDuration(10)">
                <div class="timer-icon">10</div>
                <p>Minutes</p>
                <small>Short break</small>
            </div>
            <div class="timer-option" onclick="selectBreakDuration(15)">
                <div class="timer-icon">15</div>
                <p>Minutes</p>
                <small>Extended break</small>
            </div>
        </div>
        <div id="recommendedVideos" class="recommended-videos"></div>
    </div>
</div>

<!-- Reflection Notes Modal -->
<div class="modal-overlay" id="reflectionModal">
    <div class="modal-content reflection-modal">
        <button class="modal-close" onclick="closeReflection()">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="modal-title">
            <i class="fas fa-pen me-2"></i>Reflection Notes
        </h3>
        <p class="modal-subtitle">How did this video inspire you?</p>
        <textarea class="reflection-textarea" id="reflectionText"
                  placeholder="Write your thoughts, insights, or action items..."></textarea>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeReflection()">Cancel</button>
            <button class="btn-save" onclick="saveReflection()">
                <i class="fas fa-save me-2"></i>Save Reflection
            </button>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Help -->
<div class="keyboard-help" id="keyboardHelp">
    <button class="help-toggle" onclick="toggleKeyboardHelp()">
        <i class="fas fa-keyboard"></i>
    </button>
    <div class="help-content" style="display: none;">
        <h5>Keyboard Shortcuts</h5>
        <ul>
            <li><kbd>Space</kbd> Play/Pause</li>
            <li><kbd>←</kbd> <kbd>→</kbd> Skip 5s</li>
            <li><kbd>F</kbd> Favorite</li>
            <li><kbd>Esc</kbd> Exit Focus Mode</li>
            <li><kbd>/</kbd> Search</li>
        </ul>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://www.youtube.com/iframe_api"></script>
<script>
// Video Database
const videoDatabase = [
    // Motivational Videos
    { id: 'mgmVOuLgFB0', title: 'The Power of Belief', category: 'motivational', tags: ['leadership', 'productivity'], duration: '3:47', views: 125000, type: 'short', description: 'Mindset is everything' },
    { id: 'ZXsQAXx_ao0', title: 'How Bad Do You Want It', category: 'motivational', tags: ['fitness', 'productivity'], duration: '6:23', views: 89000, type: 'short', description: 'Push beyond your limits' },
    { id: 'g-jwWYX7Jlo', title: 'Dream Big', category: 'motivational', tags: ['leadership'], duration: '12:42', views: 210000, type: 'long', description: 'Chase your biggest dreams' },
    { id: 'hbkZrOU1Zag', title: 'Steve Jobs Stanford Speech', category: 'motivational', tags: ['leadership', 'productivity'], duration: '15:04', views: 340000, type: 'long', description: 'Stay hungry, stay foolish' },

    // Entertainment - Nature & Relaxation
    { id: 'ydYDqZQpim8', title: 'Relaxing Nature Sounds', category: 'entertainment', tags: ['nature', 'wellness'], duration: '10:00', views: 45000, type: 'long', description: 'Beautiful nature scenery' },
    { id: '1ZYbU82GVz4', title: 'Ocean Waves', category: 'entertainment', tags: ['nature', 'mindfulness'], duration: '8:30', views: 67000, type: 'short', description: 'Calming ocean sounds' },

    // Entertainment - Music
    { id: 'jfKfPfyJRdk', title: 'Lofi Hip Hop', category: 'entertainment', tags: ['music'], duration: '4:12', views: 180000, type: 'short', description: 'Chill beats to relax' },
    { id: '5qap5aO4i9A', title: 'Piano Relaxation', category: 'entertainment', tags: ['music', 'wellness'], duration: '11:20', views: 92000, type: 'long', description: 'Peaceful piano music' },

    // Entertainment - Comedy
    { id: 'wKbU8B-QVZk', title: 'Funny Animal Moments', category: 'entertainment', tags: ['comedy'], duration: '4:45', views: 156000, type: 'short', description: 'Laugh out loud' },

    // Mindfulness & Wellness
    { id: 'inpok4MKVLM', title: '5 Minute Meditation', category: 'motivational', tags: ['mindfulness', 'wellness'], duration: '5:00', views: 78000, type: 'short', description: 'Quick mindfulness practice' },
    { id: 'ssss7V1_eyA', title: 'Guided Breathing', category: 'motivational', tags: ['mindfulness', 'wellness'], duration: '10:15', views: 54000, type: 'long', description: 'Deep breathing exercise' },

    // More variety
    { id: 'Ks-_Mh1QhMc', title: 'Your Elusive Creative Genius', category: 'motivational', tags: ['productivity'], duration: '19:28', views: 230000, type: 'long', description: 'TED Talk on creativity' },
];

// State Management
let currentPlayer = null;
let featuredPlayer = null;
let currentVideo = null;
let favorites = JSON.parse(localStorage.getItem('inspirationFavorites') || '[]');
let watchLater = JSON.parse(localStorage.getItem('inspirationWatchLater') || '[]');
let reflections = JSON.parse(localStorage.getItem('videoReflections') || '{}');
let isFocusMode = false;
let currentFilter = { category: 'all', type: 'all', tag: null, search: '' };

// Initialize
window.onYouTubeIframeAPIReady = function() {
    initializePage();
};

document.addEventListener('DOMContentLoaded', function() {
    if (window.YT && window.YT.Player) {
        initializePage();
    }
    setupKeyboardShortcuts();
    updateCounts();
});

function initializePage() {
    loadDailyInspiration();
    renderVideos();
    initializeFeaturedPlayer();
}

// Featured Player
function initializeFeaturedPlayer() {
    featuredPlayer = new YT.Player('featuredPlayer', {
        height: '100%',
        width: '100%',
        videoId: '',
        playerVars: {
            'playsinline': 1,
            'controls': 1,
            'rel': 0,
            'modestbranding': 1
        },
        events: {
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.PLAYING) {
        document.getElementById('featuredOverlay').style.display = 'none';
        document.getElementById('featuredInfo').style.display = 'block';
    }
}

// Load Daily Inspiration
function loadDailyInspiration() {
    const today = new Date().toDateString();
    let dailyVideo = localStorage.getItem('dailyInspirationDate');

    if (dailyVideo !== today) {
        const motivationalVideos = videoDatabase.filter(v => v.category === 'motivational');
        const randomIndex = Math.floor(Math.random() * motivationalVideos.length);
        const video = motivationalVideos[randomIndex];
        localStorage.setItem('dailyInspirationDate', today);
        localStorage.setItem('dailyInspirationVideo', JSON.stringify(video));
        renderDailyVideo(video);
    } else {
        const video = JSON.parse(localStorage.getItem('dailyInspirationVideo'));
        renderDailyVideo(video);
    }
}

function renderDailyVideo(video) {
    const card = `
        <div class="daily-video-thumbnail" onclick="playVideo('${video.id}')">
            <img src="https://img.youtube.com/vi/${video.id}/maxresdefault.jpg" alt="${video.title}">
            <div class="daily-video-overlay">
                <button class="btn-play-daily">
                    <i class="fas fa-play"></i>
                </button>
            </div>
        </div>
        <div class="daily-video-info">
            <h4>${video.title}</h4>
            <p>${video.description}</p>
            <div class="daily-tags">
                ${video.tags.map(tag => `<span class="mini-tag">${tag}</span>`).join('')}
            </div>
        </div>
    `;
    document.getElementById('dailyVideoCard').innerHTML = card;
}

// Render Videos
function renderVideos() {
    let filteredVideos = videoDatabase;

    // Apply filters
    if (currentFilter.category !== 'all') {
        filteredVideos = filteredVideos.filter(v => v.category === currentFilter.category);
    }
    if (currentFilter.type === 'short') {
        filteredVideos = filteredVideos.filter(v => v.type === 'short');
    }
    if (currentFilter.type === 'long') {
        filteredVideos = filteredVideos.filter(v => v.type === 'long');
    }
    if (currentFilter.tag) {
        filteredVideos = filteredVideos.filter(v => v.tags.includes(currentFilter.tag));
    }
    if (currentFilter.search) {
        const search = currentFilter.search.toLowerCase();
        filteredVideos = filteredVideos.filter(v =>
            v.title.toLowerCase().includes(search) ||
            v.description.toLowerCase().includes(search) ||
            v.tags.some(tag => tag.toLowerCase().includes(search))
        );
    }

    const grid = document.getElementById('videosGrid');

    if (filteredVideos.length === 0) {
        grid.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search fa-3x mb-3"></i>
                <h4>No videos found</h4>
                <p>Try adjusting your filters or search terms</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = filteredVideos.map(video => createVideoCard(video)).join('');
}

function createVideoCard(video) {
    const isFavorite = favorites.includes(video.id);
    const isInWatchLater = watchLater.includes(video.id);

    return `
        <div class="video-card" data-video-id="${video.id}">
            <div class="video-thumbnail" onclick="playVideo('${video.id}')">
                <img src="https://img.youtube.com/vi/${video.id}/mqdefault.jpg"
                     alt="${video.title}" loading="lazy">
                <div class="video-overlay">
                    <button class="btn-play-card">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
                <span class="video-duration">${video.duration}</span>
            </div>
            <div class="video-info">
                <h5 class="video-title">${video.title}</h5>
                <p class="video-description">${video.description}</p>
                <div class="video-tags">
                    ${video.tags.slice(0, 2).map(tag => `<span class="video-tag">${tag}</span>`).join('')}
                </div>
                <div class="video-stats">
                    <span class="stat-item">
                        <i class="fas fa-eye"></i> ${formatViews(video.views)}
                    </span>
                    <span class="stat-item">
                        <i class="fas fa-clock"></i> ${video.duration}
                    </span>
                </div>
                <div class="video-card-actions">
                    <button class="card-action-btn ${isFavorite ? 'active' : ''}"
                            onclick="event.stopPropagation(); toggleVideoFavorite('${video.id}')"
                            title="Favorite">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="card-action-btn ${isInWatchLater ? 'active' : ''}"
                            onclick="event.stopPropagation(); toggleWatchLater('${video.id}')"
                            title="Watch Later">
                        <i class="fas fa-bookmark"></i>
                    </button>
                    <button class="card-action-btn"
                            onclick="event.stopPropagation(); shareVideo('${video.id}')"
                            title="Share">
                        <i class="fas fa-share"></i>
                    </button>
                    <button class="card-action-btn"
                            onclick="event.stopPropagation(); openReflection('${video.id}')"
                            title="Add Reflection">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Play Video
function playVideo(videoId) {
    currentVideo = videoDatabase.find(v => v.id === videoId);
    if (!currentVideo) return;

    featuredPlayer.loadVideoById(videoId);
    document.getElementById('featuredTitle').textContent = currentVideo.title;
    document.getElementById('featuredDescription').textContent = currentVideo.description;
    document.getElementById('currentVideoTitle').textContent = currentVideo.title;
    document.getElementById('currentViews').textContent = formatViews(currentVideo.views);
    document.getElementById('currentDuration').textContent = currentVideo.duration;

    // Update favorite button
    const isFav = favorites.includes(videoId);
    document.getElementById('favoriteBtn').innerHTML = `
        <i class="fa${isFav ? 's' : 'r'} fa-heart"></i> Favorite
    `;

    // Scroll to player
    document.getElementById('featuredSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Filtering Functions
function filterByCategory(category) {
    currentFilter.category = category;
    updateActiveNav('.category-item', category);
    updateSectionTitle();
    renderVideos();
}

function filterByType(type) {
    currentFilter.type = type;
    updateActiveNav('.filter-item', type);
    renderVideos();
}

function filterByTag(tag) {
    currentFilter.tag = tag;
    currentFilter.category = 'all';
    currentFilter.type = 'all';
    updateSectionTitle(`#${tag}`);
    renderVideos();
}

function searchVideos(query) {
    currentFilter.search = query;
    const clearBtn = document.querySelector('.search-clear');
    clearBtn.style.display = query ? 'block' : 'none';
    renderVideos();
}

function clearSearch() {
    document.getElementById('videoSearch').value = '';
    currentFilter.search = '';
    document.querySelector('.search-clear').style.display = 'none';
    renderVideos();
}

function updateActiveNav(selector, value) {
    document.querySelectorAll(selector).forEach(item => {
        item.classList.remove('active');
        if (item.dataset.category === value || item.dataset.filter === value) {
            item.classList.add('active');
        }
    });
}

function updateSectionTitle(custom = null) {
    const title = document.getElementById('sectionTitle');
    if (custom) {
        title.textContent = custom;
    } else if (currentFilter.category === 'all') {
        title.textContent = 'All Videos';
    } else {
        title.textContent = currentFilter.category.charAt(0).toUpperCase() + currentFilter.category.slice(1);
    }
}

// Favorites Management
function toggleVideoFavorite(videoId) {
    const index = favorites.indexOf(videoId);
    if (index > -1) {
        favorites.splice(index, 1);
    } else {
        favorites.push(videoId);
    }
    localStorage.setItem('inspirationFavorites', JSON.stringify(favorites));
    updateCounts();
    renderVideos();
}

function toggleFavorite() {
    if (!currentVideo) return;
    toggleVideoFavorite(currentVideo.id);
    const isFav = favorites.includes(currentVideo.id);
    document.getElementById('favoriteBtn').innerHTML = `
        <i class="fa${isFav ? 's' : 'r'} fa-heart"></i> Favorite
    `;
}

function showFavorites() {
    const favVideos = videoDatabase.filter(v => favorites.includes(v.id));
    const grid = document.getElementById('videosGrid');

    if (favVideos.length === 0) {
        grid.innerHTML = `
            <div class="no-results">
                <i class="fas fa-heart fa-3x mb-3"></i>
                <h4>No favorites yet</h4>
                <p>Click the heart icon on videos to save them here</p>
            </div>
        `;
    } else {
        grid.innerHTML = favVideos.map(video => createVideoCard(video)).join('');
    }
    document.getElementById('sectionTitle').textContent = 'My Favorites';
}

// Watch Later
function toggleWatchLater(videoId) {
    const index = watchLater.indexOf(videoId);
    if (index > -1) {
        watchLater.splice(index, 1);
    } else {
        watchLater.push(videoId);
    }
    localStorage.setItem('inspirationWatchLater', JSON.stringify(watchLater));
    updateCounts();
    renderVideos();
}

function addToWatchLater() {
    if (!currentVideo) return;
    toggleWatchLater(currentVideo.id);
    showNotification('Added to Watch Later!');
}

function showWatchLater() {
    const videos = videoDatabase.filter(v => watchLater.includes(v.id));
    const grid = document.getElementById('videosGrid');

    if (videos.length === 0) {
        grid.innerHTML = `
            <div class="no-results">
                <i class="fas fa-bookmark fa-3x mb-3"></i>
                <h4>Watch Later is empty</h4>
                <p>Save videos to watch them later</p>
            </div>
        `;
    } else {
        grid.innerHTML = videos.map(video => createVideoCard(video)).join('');
    }
    document.getElementById('sectionTitle').textContent = 'Watch Later';
}

function updateCounts() {
    document.getElementById('favCount').textContent = favorites.length;
    document.getElementById('watchLaterCount').textContent = watchLater.length;
}

// Random Video
function playRandomVideo() {
    const randomIndex = Math.floor(Math.random() * videoDatabase.length);
    const video = videoDatabase[randomIndex];
    playVideo(video.id);
    showNotification('🎲 Playing a random video!');
}

// Break Timer
function showBreakTimer() {
    document.getElementById('breakTimerModal').classList.add('show');
}

function closeBreakTimer() {
    document.getElementById('breakTimerModal').classList.remove('show');
}

function selectBreakDuration(minutes) {
    const maxDuration = minutes * 60;
    const recommended = videoDatabase.filter(v => {
        const [min, sec] = v.duration.split(':').map(Number);
        const totalSeconds = (min || 0) * 60 + (sec || 0);
        return totalSeconds <= maxDuration;
    });

    const html = `
        <h4 class="mt-4">Recommended for ${minutes} minutes</h4>
        <div class="recommended-grid">
            ${recommended.slice(0, 3).map(v => `
                <div class="recommended-card" onclick="playVideo('${v.id}'); closeBreakTimer();">
                    <img src="https://img.youtube.com/vi/${v.id}/mqdefault.jpg" alt="${v.title}">
                    <div class="recommended-info">
                        <h6>${v.title}</h6>
                        <small>${v.duration}</small>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    document.getElementById('recommendedVideos').innerHTML = html;
}

// Reflection
let currentReflectionVideo = null;

function openReflection(videoId) {
    currentReflectionVideo = videoId;
    const existing = reflections[videoId] || '';
    document.getElementById('reflectionText').value = existing;
    document.getElementById('reflectionModal').classList.add('show');
}

function closeReflection() {
    document.getElementById('reflectionModal').classList.remove('show');
    currentReflectionVideo = null;
}

function saveReflection() {
    const text = document.getElementById('reflectionText').value;
    if (text && currentReflectionVideo) {
        reflections[currentReflectionVideo] = text;
        localStorage.setItem('videoReflections', JSON.stringify(reflections));
        showNotification('Reflection saved!');
        closeReflection();
    }
}

// Focus Mode
function toggleFocusMode() {
    isFocusMode = !isFocusMode;
    const container = document.getElementById('inspirationHub');

    if (isFocusMode) {
        container.classList.add('focus-mode');
        document.getElementById('inspirationSidebar').style.display = 'none';
        document.getElementById('featuredSection').requestFullscreen?.();
    } else {
        container.classList.remove('focus-mode');
        document.getElementById('inspirationSidebar').style.display = '';
        if (document.fullscreenElement) {
            document.exitFullscreen();
        }
    }
}

// Keyboard Shortcuts
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ignore if typing in input
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            if (e.key === '/') {
                e.preventDefault();
                document.getElementById('videoSearch').focus();
            }
            return;
        }

        switch(e.key.toLowerCase()) {
            case ' ':
                e.preventDefault();
                if (featuredPlayer) {
                    const state = featuredPlayer.getPlayerState();
                    if (state === 1) featuredPlayer.pauseVideo();
                    else featuredPlayer.playVideo();
                }
                break;
            case 'f':
                e.preventDefault();
                toggleFavorite();
                break;
            case 'escape':
                if (isFocusMode) toggleFocusMode();
                break;
            case 'arrowleft':
                if (featuredPlayer) featuredPlayer.seekTo(featuredPlayer.getCurrentTime() - 5);
                break;
            case 'arrowright':
                if (featuredPlayer) featuredPlayer.seekTo(featuredPlayer.getCurrentTime() + 5);
                break;
        }
    });
}

function toggleKeyboardHelp() {
    const content = document.querySelector('.help-content');
    content.style.display = content.style.display === 'none' ? 'block' : 'none';
}

// Utilities
function formatViews(views) {
    if (views >= 1000000) return (views / 1000000).toFixed(1) + 'M';
    if (views >= 1000) return (views / 1000).toFixed(1) + 'K';
    return views.toString();
}

function shareVideo(videoId) {
    const video = videoDatabase.find(v => v.id === videoId);
    if (navigator.share) {
        navigator.share({
            title: video.title,
            text: video.description,
            url: `https://www.youtube.com/watch?v=${videoId}`
        });
    } else {
        navigator.clipboard.writeText(`https://www.youtube.com/watch?v=${videoId}`);
        showNotification('Link copied to clipboard!');
    }
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'toast-notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 100);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function toggleLike() {
    showNotification('Thank you for your feedback!');
}

function setView(type) {
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.view-btn').classList.add('active');
    const grid = document.getElementById('videosGrid');
    grid.className = type === 'grid' ? 'videos-grid' : 'videos-list';
}

function playFeaturedVideo() {
    if (featuredPlayer) {
        featuredPlayer.playVideo();
    }
}
</script>
@endpush

@push('styles')
<style>
/* Inspiration Hub - Calming Color Scheme */
:root {
    --inspiration-primary: #6366f1;
    --inspiration-secondary: #8b5cf6;
    --inspiration-accent: #ec4899;
    --inspiration-success: #10b981;
    --inspiration-warning: #f59e0b;
    --inspiration-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --inspiration-light: #f0f4ff;
    --inspiration-dark: #1e293b;
}

body {
    background: #f8fafc;
}

/* Main Container */
.inspiration-container {
    min-height: 100vh;
    background: linear-gradient(to bottom, #f0f4ff 0%, #faf5ff 100%);
    padding-bottom: 3rem;
}

/* Header */
.inspiration-header {
    background: var(--inspiration-bg);
    padding: 2rem 0;
    border-radius: 0 0 30px 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
    margin-bottom: 2rem;
}

.inspiration-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.inspiration-subtitle {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-size: 1.1rem;
}

.header-controls {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.btn-timer, .btn-random, .btn-focus {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    backdrop-filter: blur(10px);
}

.btn-timer:hover, .btn-random:hover, .btn-focus:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Sidebar */
.inspiration-sidebar {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 20px;
}

.sidebar-section {
    margin-bottom: 2rem;
}

.sidebar-section:last-child {
    margin-bottom: 0;
}

.sidebar-heading {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 1px;
    margin-bottom: 1rem;
}

.category-nav, .filter-nav, .library-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-item, .filter-item, .library-item {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #475569;
    font-weight: 500;
}

.category-item:hover, .filter-item:hover, .library-item:hover {
    background: var(--inspiration-light);
    color: var(--inspiration-primary);
    transform: translateX(5px);
}

.category-item.active, .filter-item.active {
    background: linear-gradient(135deg, var(--inspiration-primary) 0%, var(--inspiration-secondary) 100%);
    color: white;
}

.count {
    background: var(--inspiration-primary);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: auto;
}

.tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-badge {
    padding: 0.5rem 1rem;
    background: var(--inspiration-light);
    color: var(--inspiration-primary);
    border-radius: 20px;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.tag-badge:hover {
    background: var(--inspiration-primary);
    color: white;
    transform: translateY(-2px);
}

/* Search */
.search-section {
    margin-bottom: 2rem;
}

.search-bar {
    position: relative;
    max-width: 600px;
}

.search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.125rem;
}

.search-input {
    width: 100%;
    padding: 1rem 3rem 1rem 3.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    font-size: 1rem;
    transition: all 0.3s;
    background: white;
}

.search-input:focus {
    outline: none;
    border-color: var(--inspiration-primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.search-clear {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s;
}

.search-clear:hover {
    background: #f1f5f9;
    color: #64748b;
}

/* Daily Inspiration */
.daily-inspiration-section {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 30px rgba(245, 158, 11, 0.3);
}

.daily-banner {
    margin-bottom: 1.5rem;
}

.daily-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.daily-icon {
    font-size: 3rem;
    color: white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.daily-title {
    color: white;
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
}

.daily-subtitle {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.daily-video-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    gap: 1.5rem;
    align-items: center;
    padding: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.daily-video-thumbnail {
    position: relative;
    flex-shrink: 0;
    width: 280px;
    height: 160px;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
}

.daily-video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.daily-video-thumbnail:hover img {
    transform: scale(1.05);
}

.daily-video-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.daily-video-thumbnail:hover .daily-video-overlay {
    opacity: 1;
}

.btn-play-daily {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    border: none;
    color: var(--inspiration-primary);
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-play-daily:hover {
    transform: scale(1.1);
}

.daily-video-info h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.daily-video-info p {
    color: #64748b;
    margin-bottom: 1rem;
}

.daily-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mini-tag {
    padding: 0.25rem 0.75rem;
    background: var(--inspiration-light);
    color: var(--inspiration-primary);
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Featured Video - Continued in next part due to length */
</style>
<link rel="stylesheet" href="{{ asset('css/inspiration.css') }}">
@endpush
