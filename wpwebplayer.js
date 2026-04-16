(function() {
    'use strict';
    
    // 检查是否已经初始化过
    if (window.wpMusicPlayerInitialized) {
        return;
    }
    
    class WPMusicPlayer {
        constructor(options = {}) {
            this.options = {
                src: options.src || '',
                title: options.title || '我的音乐',
                artist: options.artist || '艺术家',
                cover: options.cover || '',
                loop: options.loop !== false,
                autoplay: options.autoplay || false,
                volume: options.volume || 0.5,
                fixed: options.fixed !== false,
                mini: options.mini !== false,
                theme: options.theme || '#ff6b6b'
            };

            this.isPlaying = false;
            this.isDragging = false;

            this.createPlayer();

            // 恢复播放状态
            try {
                const state = JSON.parse(localStorage.getItem('wpMusicPlayerState'));
                console.log('[WPMusicPlayer] 恢复状态:', state, '当前src:', this.options.src);
                if (state && state.src === this.options.src) {
                    this.audio.currentTime = state.currentTime || 0;
                    this.shouldAutoPlay = !!state.isPlaying;
                    console.log('[WPMusicPlayer] 恢复进度:', this.audio.currentTime, 'shouldAutoPlay:', this.shouldAutoPlay);
                } else {
                    this.shouldAutoPlay = !!this.options.autoplay;
                    console.log('[WPMusicPlayer] 没有可恢复状态，autoplay:', this.shouldAutoPlay);
                }
            } catch (e) {
                this.shouldAutoPlay = !!this.options.autoplay;
                console.log('[WPMusicPlayer] 状态恢复异常，autoplay:', this.shouldAutoPlay, e);
            }

            this.bindEvents();
        }
        
        createPlayer() {
            // 创建播放器HTML结构
            const playerHTML = `
                <div class="wp-music-player" id="wpMusicPlayer">
                    <div class="player-cover">
                        <div class="play-btn" id="wpPlayBtn">
                            <div class="play-icon" id="wpPlayIcon"></div>
                        </div>
                    </div>
                    
                    <div class="player-info">
                        <div class="song-title" id="wpSongTitle">${this.options.title}</div>
                        <div class="song-artist" id="wpSongArtist">${this.options.artist}</div>
                        
                        <div class="progress-container" id="wpProgressContainer">
                            <div class="progress-bar" id="wpProgressBar"></div>
                        </div>
                        
                        <div class="time-display">
                            <span id="wpCurrentTime">0:00</span>
                            <span id="wpTotalTime">0:00</span>
                        </div>
                        
                        <div class="controls">
                            <button class="control-btn" id="wpMuteBtn" title="静音">🔊</button>
                            <input type="range" class="volume-slider" id="wpVolumeSlider" min="0" max="100" value="${this.options.volume * 100}">
                            <span class="volume-display" id="wpVolumeDisplay">${Math.round(this.options.volume * 100)}</span>
                        </div>
                    </div>
                </div>
                
                <audio id="wpAudioPlayer" preload="metadata" ${this.options.loop ? 'loop' : ''}>
                    <source src="${this.options.src}" type="audio/mpeg">
                    您的浏览器不支持音频播放。
                </audio>
            `;
            
            // 插入到页面中
            document.body.insertAdjacentHTML('beforeend', playerHTML);
            
            // 获取元素引用
            this.audio = document.getElementById('wpAudioPlayer');
            this.playBtn = document.getElementById('wpPlayBtn');
            this.playIcon = document.getElementById('wpPlayIcon');
            this.musicPlayer = document.getElementById('wpMusicPlayer');
            this.progressContainer = document.getElementById('wpProgressContainer');
            this.progressBar = document.getElementById('wpProgressBar');
            this.currentTime = document.getElementById('wpCurrentTime');
            this.totalTime = document.getElementById('wpTotalTime');
            this.volumeSlider = document.getElementById('wpVolumeSlider');
            this.volumeDisplay = document.getElementById('wpVolumeDisplay');
            this.muteBtn = document.getElementById('wpMuteBtn');
            this.songTitle = document.getElementById('wpSongTitle');
            this.songArtist = document.getElementById('wpSongArtist');
            this.playerCover = this.musicPlayer.querySelector('.player-cover');

            if (this.options.cover) {
                this.playerCover.style.backgroundImage = `url('${this.options.cover}')`;
                this.playerCover.style.backgroundSize = 'cover';
                this.playerCover.style.backgroundPosition = 'center';
            }
        }
        
        bindEvents() {
            // 检查必要元素是否存在
            if (!this.audio || !this.playBtn) {
                console.log('音乐播放器元素未找到');
                return;
            }
            
            // 播放/暂停按钮事件
            this.playBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.togglePlay();
            });
            
            // 音频事件
            this.audio.addEventListener('loadedmetadata', () => {
                this.totalTime.textContent = this.formatTime(this.audio.duration);
            });
            
            this.audio.addEventListener('timeupdate', () => {
                if (!this.isDragging) {
                    this.updateProgress();
                }
            });
            
            this.audio.addEventListener('ended', () => {
                // 歌曲结束后的处理
                this.isPlaying = false;
                this.playIcon.classList.remove('playing');
                this.musicPlayer.classList.remove('playing');
            });
            
            this.audio.addEventListener('play', () => {
                this.isPlaying = true;
                this.playIcon.classList.add('playing');
                this.musicPlayer.classList.add('playing');
            });
            
            this.audio.addEventListener('pause', () => {
                this.isPlaying = false;
                this.playIcon.classList.remove('playing');
                this.musicPlayer.classList.remove('playing');
            });
            
            // 错误处理
            this.audio.addEventListener('error', (e) => {
                console.log('音频加载错误:', e);
            });
            
            // 进度条事件
            this.progressContainer.addEventListener('click', (e) => {
                this.seekTo(e);
            });
            
            this.progressContainer.addEventListener('mousedown', (e) => {
                this.isDragging = true;
                this.seekTo(e);
            });
            
            document.addEventListener('mousemove', (e) => {
                if (this.isDragging) {
                    this.seekTo(e);
                }
            });
            
            document.addEventListener('mouseup', () => {
                this.isDragging = false;
            });
            
            // 音量控制
            this.volumeSlider.addEventListener('input', (e) => {
                const volume = e.target.value / 100;
                this.audio.volume = volume;
                this.volumeDisplay.textContent = Math.round(volume * 100);
                this.saveVolume(volume);
            });
            
            // 静音按钮
            this.muteBtn.addEventListener('click', () => {
                this.toggleMute();
            });
            
            // 设置初始音量
            this.audio.volume = this.options.volume;
            
            // 加载保存的音量设置
            this.loadVolume();
            
            // 自动播放（需要用户交互）
            console.log('[WPMusicPlayer] bindEvents shouldAutoPlay:', this.shouldAutoPlay);
            if (this.shouldAutoPlay) {
                setTimeout(() => {
                    console.log('[WPMusicPlayer] 执行自动播放');
                    this.play();
                }, 500);
            }
        }
        
        togglePlay() {
            if (this.isPlaying) {
                this.pause();
            } else {
                this.play();
            }
        }
        
        play() {
            this.audio.play().then(() => {
                this.isPlaying = true;
                this.playIcon.classList.add('playing');
                this.musicPlayer.classList.add('playing');
            }).catch(error => {
                console.log('播放失败:', error);
                // 提示用户需要交互才能播放
                if (error.name === 'NotAllowedError') {
                    console.log('需要用户交互才能播放音频');
                }
            });
        }
        
        pause() {
            this.audio.pause();
            this.isPlaying = false;
            this.playIcon.classList.remove('playing');
            this.musicPlayer.classList.remove('playing');
        }
        
        updateProgress() {
            if (this.audio.duration) {
                const progress = (this.audio.currentTime / this.audio.duration) * 100;
                this.progressBar.style.width = `${progress}%`;
                this.currentTime.textContent = this.formatTime(this.audio.currentTime);
            }
        }
        
        seekTo(e) {
            if (!this.audio.duration) return;
            
            const rect = this.progressContainer.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const width = rect.width;
            const clickRatio = clickX / width;
            
            if (clickRatio >= 0 && clickRatio <= 1) {
                this.audio.currentTime = clickRatio * this.audio.duration;
            }
        }
        
        toggleMute() {
            if (this.audio.muted) {
                this.audio.muted = false;
                this.muteBtn.textContent = '🔊';
            } else {
                this.audio.muted = true;
                this.muteBtn.textContent = '🔇';
            }
        }
        
        formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
        
        saveVolume(volume) {
            try {
                localStorage.setItem('wpMusicPlayerVolume', volume);
            } catch (e) {
                // localStorage不可用
            }
        }
        
        loadVolume() {
            try {
                const savedVolume = localStorage.getItem('wpMusicPlayerVolume');
                if (savedVolume) {
                    const volume = parseFloat(savedVolume);
                    this.audio.volume = volume;
                    this.volumeSlider.value = volume * 100;
                    this.volumeDisplay.textContent = Math.round(volume * 100);
                }
            } catch (e) {
                // localStorage不可用
            }
        }
        
        // 公共API方法
        setTitle(title) {
            this.songTitle.textContent = title;
        }
        
        setArtist(artist) {
            this.songArtist.textContent = artist;
        }
        
        setSrc(src) {
            this.audio.src = src;
        }
        
        destroy() {
            if (this.audio) {
                // 保存播放状态和进度，统一用 this.options.src
                try {
                    localStorage.setItem('wpMusicPlayerState', JSON.stringify({
                        src: this.options.src, // 这里改为 this.options.src
                        currentTime: this.audio.currentTime,
                        isPlaying: this.isPlaying
                    }));
                } catch (e) {}
                this.audio.remove();
            }
            if (this.musicPlayer) {
                this.musicPlayer.remove();
            }
        }
    }
    
    // 自定义元素支持
    if (typeof customElements !== 'undefined') {
        class WPMusicPlayerElement extends HTMLElement {
            connectedCallback() {
                const options = {
                    src: this.getAttribute('src') || '',
                    title: this.getAttribute('title') || '我的音乐',
                    artist: this.getAttribute('artist') || '艺术家',
                    cover: this.getAttribute('cover') || '',
                    loop: this.getAttribute('loop') !== 'false',
                    autoplay: this.getAttribute('autoplay') === 'true',
                    volume: parseFloat(this.getAttribute('volume')) || 0.5,
                    fixed: this.getAttribute('fixed') !== 'false',
                    mini: this.getAttribute('mini') !== 'false',
                    theme: this.getAttribute('theme') || '#ff6b6b'
                };
                
                this.player = new WPMusicPlayer(options);
            }
            
            disconnectedCallback() {
                if (this.player) {
                    this.player.destroy();
                }
            }
        }
        
        customElements.define('wp-music-player', WPMusicPlayerElement);
    }
    
    // 全局API
    window.WPMusicPlayer = WPMusicPlayer;
    window.wpMusicPlayerInitialized = true;
    
    // 自动初始化（查找页面中的配置）
    function autoInit() {
        // 查找页面中的配置脚本
        const configScript = document.querySelector('script[data-wp-music-config]');
        if (configScript) {
            try {
                const config = JSON.parse(configScript.textContent);
                new WPMusicPlayer(config);
            } catch (e) {
                console.log('音乐播放器配置解析失败:', e);
            }
        }
    }
    
    // DOM 加载完成后自动初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }
    
})();
