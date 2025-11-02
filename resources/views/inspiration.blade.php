@extends('layouts.app')

@section('title', 'Inspiration Hub - MyTime')

@section('content')
<div class="inspiration-container" id="inspirationHub">
    <!-- Header Section -->
    <div class="inspiration-header mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas fa-star-half-alt me-2 text-warning"></i>Inspiration Hub
                    </h1>
                    <p class="text-muted mb-0">Recharge your mind, fuel your motivation</p>
                </div>
                <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                        <!-- Break Timer -->
                        <button class="btn btn-outline-primary btn-sm" onclick="showBreakTimer()">
                            <i class="fas fa-clock me-2"></i>Take a Break
                        </button>
                        <!-- Random Video -->
                        <button class="btn btn-outline-success btn-sm" onclick="playRandomVideo()">
                            <i class="fas fa-random me-2"></i>Surprise Me
                        </button>
                        <!-- Focus Mode -->
                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleFocusMode()" title="Focus Mode (F)">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-2 d-none d-lg-block" id="inspirationSidebar">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <!-- Categories -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Categories</h6>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-sm btn-outline-primary text-start category-btn active" data-category="all" onclick="filterByCategory('all')">
                                    <i class="fas fa-th me-2"></i>All Videos
                                </button>
                                <button class="btn btn-sm btn-outline-primary text-start category-btn" data-category="motivational" onclick="filterByCategory('motivational')">
                                    <i class="fas fa-fire me-2"></i>Motivational
                                </button>
                                <button class="btn btn-sm btn-outline-primary text-start category-btn" data-category="entertainment" onclick="filterByCategory('entertainment')">
                                    <i class="fas fa-smile me-2"></i>Entertainment
                                </button>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Filters</h6>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-sm btn-outline-secondary text-start filter-btn active" data-filter="all" onclick="filterByType('all')">
                                    <i class="fas fa-star me-2"></i>Most Popular
                                </button>
                                <button class="btn btn-sm btn-outline-secondary text-start filter-btn" data-filter="recent" onclick="filterByType('recent')">
                                    <i class="fas fa-clock me-2"></i>Recently Added
                                </button>
                                <button class="btn btn-sm btn-outline-secondary text-start filter-btn" data-filter="short" onclick="filterByType('short')">
                                    <i class="fas fa-bolt me-2"></i>Short (< 5 min)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary text-start filter-btn" data-filter="long" onclick="filterByType('long')">
                                    <i class="fas fa-film me-2"></i>Long (10+ min)
                                </button>
                            </div>
                        </div>

                        <!-- My Library -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">My Library</h6>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-sm btn-outline-danger text-start" onclick="showFavorites()">
                                    <i class="fas fa-heart me-2"></i>Favorites <span class="badge bg-danger ms-auto" id="favCount">0</span>
                                </button>
                                <button class="btn btn-sm btn-outline-info text-start" onclick="showWatchLater()">
                                    <i class="fas fa-bookmark me-2"></i>Watch Later <span class="badge bg-info ms-auto" id="watchLaterCount">0</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Tags</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('leadership')">Leadership</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('mindfulness')">Mindfulness</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('fitness')">Fitness</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('music')">Music</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('comedy')">Comedy</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('nature')">Nature</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('productivity')">Productivity</button>
                                <button class="btn btn-xs btn-outline-primary" onclick="filterByTag('wellness')">Wellness</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10">
                <!-- Search Bar -->
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" id="videoSearch" class="form-control" placeholder="Search for inspiration, relaxation, or entertainment..." oninput="searchVideos(this.value)">
                        <button class="btn btn-outline-secondary" onclick="clearSearch()" id="searchClear" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Daily Inspiration -->
                <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="font-size: 2rem;">☀️</div>
                            <div>
                                <h5 class="mb-0 text-white">Daily Inspiration</h5>
                                <small class="text-white-50">Your motivation boost for today</small>
                            </div>
                        </div>
                        <div id="dailyVideoCard" class="d-flex gap-3 align-items-center">
                            <!-- Daily video will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Featured Video Player -->
                <div class="card border-0 shadow-sm mb-4" id="featuredSection">
                    <div class="card-body p-0">
                        <div id="featuredPlayer" style="width: 100%; height: 400px; background: #000;"></div>
                        <div class="p-4">
                            <h5 id="currentVideoTitle">Select a video to begin</h5>
                            <p class="text-muted mb-3" id="currentVideoDescription">Choose from our curated collection below</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-sm btn-outline-primary" id="likeBtn" onclick="toggleLike()">
                                    <i class="far fa-thumbs-up me-2"></i>Like
                                </button>
                                <button class="btn btn-sm btn-outline-danger" id="favoriteBtn" onclick="toggleFavorite()">
                                    <i class="far fa-heart me-2"></i>Favorite
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="addToWatchLater()">
                                    <i class="far fa-bookmark me-2"></i>Watch Later
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="shareVideo()">
                                    <i class="fas fa-share me-2"></i>Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Videos Grid -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" id="sectionTitle">All Videos</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-secondary active" onclick="setView('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="setView('list')">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3" id="videosGrid">
                        <!-- Videos will be loaded here -->
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-3">Loading inspiring content...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Break Timer Modal -->
<div class="modal fade" id="breakTimerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title">
                    <i class="fas fa-coffee me-2"></i>Take a Break
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Choose your break duration</p>
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <button class="btn btn-outline-primary w-100 py-3" onclick="selectBreakDuration(5)">
                            <div class="h5 mb-1">5</div>
                            <small>Minutes</small>
                        </button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-outline-primary w-100 py-3" onclick="selectBreakDuration(10)">
                            <div class="h5 mb-1">10</div>
                            <small>Minutes</small>
                        </button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-outline-primary w-100 py-3" onclick="selectBreakDuration(15)">
                            <div class="h5 mb-1">15</div>
                            <small>Minutes</small>
                        </button>
                    </div>
                </div>
                <div id="recommendedVideos"></div>
            </div>
        </div>
    </div>
</div>

<!-- Reflection Notes Modal -->
<div class="modal fade" id="reflectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title">
                    <i class="fas fa-pen me-2"></i>Reflection Notes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">How did this video inspire you?</p>
                <textarea class="form-control" id="reflectionText" rows="4" placeholder="Write your thoughts, insights, or action items..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveReflection()">
                    <i class="fas fa-save me-2"></i>Save Reflection
                </button>
            </div>
        </div>
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
    // Player state changed
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
        <div style="flex-shrink: 0; width: 200px; height: 120px; border-radius: 12px; overflow: hidden; cursor: pointer; position: relative;" onclick="playVideo('${video.id}')">
            <img src="https://img.youtube.com/vi/${video.id}/mqdefault.jpg" alt="${video.title}" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;" class="daily-overlay">
                <button style="width: 50px; height: 50px; border-radius: 50%; background: white; border: none; color: #f59e0b; font-size: 1.25rem; cursor: pointer;">
                    <i class="fas fa-play"></i>
                </button>
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-1 text-white">${video.title}</h6>
            <p class="text-white-50 small mb-2">${video.description}</p>
            <div class="d-flex gap-2 flex-wrap">
                ${video.tags.map(tag => `<span class="badge bg-white text-warning">${tag}</span>`).join('')}
            </div>
        </div>
    `;
    document.getElementById('dailyVideoCard').innerHTML = card;
    
    // Add hover effect
    document.querySelector('.daily-overlay').parentElement.addEventListener('mouseenter', function() {
        this.querySelector('.daily-overlay').style.opacity = '1';
    });
    document.querySelector('.daily-overlay').parentElement.addEventListener('mouseleave', function() {
        this.querySelector('.daily-overlay').style.opacity = '0';
    });
}

// Render Videos
function renderVideos() {
    let filteredVideos = videoDatabase;

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
            <div class="col-12 text-center py-5">
                <i class="fas fa-search" style="font-size: 3rem; color: #d1d5db;"></i>
                <h5 class="mt-3 text-muted">No videos found</h5>
                <p class="text-muted">Try adjusting your filters or search terms</p>
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
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 video-card" data-video-id="${video.id}">
                <div style="position: relative; height: 200px; overflow: hidden; background: #000; cursor: pointer;" onclick="playVideo('${video.id}')">
                    <img src="https://img.youtube.com/vi/${video.id}/mqdefault.jpg" alt="${video.title}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;" class="video-overlay">
                        <button style="width: 60px; height: 60px; border-radius: 50%; background: white; border: none; color: #667eea; font-size: 1.5rem; cursor: pointer;">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                    <span class="badge bg-dark position-absolute bottom-0 end-0 m-2">${video.duration}</span>
                </div>
                <div class="card-body">
                    <h6 class="card-title">${video.title}</h6>
                    <p class="card-text small text-muted mb-2">${video.description}</p>
                    <div class="d-flex gap-1 flex-wrap mb-3">
                        ${video.tags.slice(0, 2).map(tag => `<span class="badge bg-light text-dark">${tag}</span>`).join('')}
                    </div>
                    <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                        <span><i class="fas fa-eye me-1"></i>${formatViews(video.views)}</span>
                        <span><i class="fas fa-clock me-1"></i>${video.duration}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-danger flex-grow-1 ${isFavorite ? 'active' : ''}" onclick="event.stopPropagation(); toggleVideoFavorite('${video.id}')">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info flex-grow-1 ${isInWatchLater ? 'active' : ''}" onclick="event.stopPropagation(); toggleWatchLater('${video.id}')">
                            <i class="fas fa-bookmark"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1" onclick="event.stopPropagation(); shareVideo('${video.id}')">
                            <i class="fas fa-share"></i>
                        </button>
                    </div>
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
    document.getElementById('currentVideoTitle').textContent = currentVideo.title;
    document.getElementById('currentVideoDescription').textContent = currentVideo.description;

    const isFav = favorites.includes(videoId);
    document.getElementById('favoriteBtn').innerHTML = `
        <i class="fa${isFav ? 's' : 'r'} fa-heart me-2"></i>Favorite
    `;

    document.getElementById('featuredSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Filtering Functions
function filterByCategory(category) {
    currentFilter.category = category;
    document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-category="${category}"]`).classList.add('active');
    updateSectionTitle();
    renderVideos();
}

function filterByType(type) {
    currentFilter.type = type;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-filter="${type}"]`).classList.add('active');
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
    document.getElementById('searchClear').style.display = query ? 'block' : 'none';
    renderVideos();
}

function clearSearch() {
    document.getElementById('videoSearch').value = '';
    currentFilter.search = '';
    document.getElementById('searchClear').style.display = 'none';
    renderVideos();
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
        <i class="fa${isFav ? 's' : 'r'} fa-heart me-2"></i>Favorite
    `;
}

function showFavorites() {
    const favVideos = videoDatabase.filter(v => favorites.includes(v.id));
    const grid = document.getElementById('videosGrid');

    if (favVideos.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-heart" style="font-size: 3rem; color: #d1d5db;"></i>
                <h5 class="mt-3 text-muted">No favorites yet</h5>
                <p class="text-muted">Click the heart icon on videos to save them here</p>
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
            <div class="col-12 text-center py-5">
                <i class="fas fa-bookmark" style="font-size: 3rem; color: #d1d5db;"></i>
                <h5 class="mt-3 text-muted">Watch Later is empty</h5>
                <p class="text-muted">Save videos to watch them later</p>
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
    const modal = new bootstrap.Modal(document.getElementById('breakTimerModal'));
    modal.show();
}

function selectBreakDuration(minutes) {
    const maxDuration = minutes * 60;
    const recommended = videoDatabase.filter(v => {
        const [min, sec] = v.duration.split(':').map(Number);
        const totalSeconds = (min || 0) * 60 + (sec || 0);
        return totalSeconds <= maxDuration;
    });

    const html = `
        <h6 class="mb-3">Recommended for ${minutes} minutes</h6>
        <div class="row g-2">
            ${recommended.slice(0, 3).map(v => `
                <div class="col-12">
                    <button class="btn btn-outline-primary w-100 text-start" onclick="playVideo('${v.id}'); bootstrap.Modal.getInstance(document.getElementById('breakTimerModal')).hide();">
                        <img src="https://img.youtube.com/vi/${v.id}/default.jpg" alt="${v.title}" style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <div class="small fw-semibold">${v.title}</div>
                            <small class="text-muted">${v.duration}</small>
                        </div>
                    </button>
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
    const modal = new bootstrap.Modal(document.getElementById('reflectionModal'));
    modal.show();
}

function saveReflection() {
    const text = document.getElementById('reflectionText').value;
    if (text && currentReflectionVideo) {
        reflections[currentReflectionVideo] = text;
        localStorage.setItem('videoReflections', JSON.stringify(reflections));
        showNotification('Reflection saved!');
        bootstrap.Modal.getInstance(document.getElementById('reflectionModal')).hide();
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
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 3000);
}

function toggleLike() {
    showNotification('Thank you for your feedback!');
}

function setView(type) {
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.btn').classList.add('active');
}
</script>
@endpush

@push('styles')
<style>
    .inspiration-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem 0;
        border-radius: 0 0 24px 24px;
        color: white;
    }

    .inspiration-container {
        min-height: 100vh;
        background: linear-gradient(to bottom, #f0f4ff 0%, #faf5ff 100%);
        padding-bottom: 3rem;
    }

    .category-btn.active,
    .filter-btn.active {
        background-color: #667eea !important;
        color: white !important;
        border-color: #667eea !important;
    }

    .video-overlay {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .video-card:hover .video-overlay {
        opacity: 1;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .focus-mode {
        position: fixed;
        inset: 0;
        z-index: 9999;
    }

    [x-cloak] { display: none !important; }
</style>
@endpush
