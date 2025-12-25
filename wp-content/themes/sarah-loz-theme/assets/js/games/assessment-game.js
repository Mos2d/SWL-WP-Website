console.log('🔌 Phase 13: Robust Scoring Fix Loaded');

class AssessmentGame {
    constructor(containerId, config) {
        this.container = document.getElementById(containerId);
        this.config = config;
        this.currentIndex = 0;
        this.startTime = Date.now();
        
        // 1. Initialize Scoreboard
        this.scores = { total: 0 };
        this.totals = { total: this.config.questions.length };
        
        // 2. Scan questions to build the "Totals" (Denominators)
        this.config.questions.forEach((q, index) => {
            const cat = this.normalizeCategory(q.criteria_category);
            
            // Initialize this category if new
            if (this.totals[cat] === undefined) {
                this.totals[cat] = 0;
                this.scores[cat] = 0;
            }
            this.totals[cat]++;
            
            // Debug Log to prove we see the categories
            console.log(`Question ${index + 1}: Category detected as "[${cat}]"`);
        });

        console.log("📊 Scoreboard Ready:", this.totals);
    }

    // Helper to ensure category names are ALWAYS the same
    normalizeCategory(raw) {
        if (!raw) return 'general';
        return raw.toString().toLowerCase().trim();
    }

    init() {
        if (!this.container) return;

        // Reset Container
        this.container.innerHTML = '';
        this.container.style.display = 'block';
        this.container.style.minHeight = '600px';

        // Remove old loaders
        const oldLoader = document.getElementById('game-loader');
        if(oldLoader) oldLoader.remove();
        document.querySelectorAll('.game-loader').forEach(l => l.remove());

        this.renderStartScreen();
    }

    renderStartScreen() {
        const content = document.createElement('div');
        content.style.cssText = "display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; min-height:400px; text-align:center; padding: 20px;";
        
        let html = `
            <div style="margin-bottom:30px;">
                <h1 style="color:#2d3748; font-size: 2rem; margin-bottom: 15px;">📝 اختبار تحديد المستوى</h1>
                <p style="color:#718096; font-size: 1.1rem;">عدد الأسئلة: <strong>${this.config.questions.length}</strong></p>
            </div>
        `;

        if (this.config.introAudio) {
            html += `
                <div style="background:#f7fafc; padding:20px; border-radius:15px; margin-bottom:30px; border:1px solid #edf2f7; width:100%; max-width:500px;">
                    <p style="color:#4a5568; margin-bottom:10px; font-weight:bold;">🔊 تعليمات الاختبار</p>
                    <audio controls src="${this.config.introAudio}" style="width:100%;"></audio>
                </div>
            `;
        }

        html += `
            <button id="btn-start" style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white; border: none; padding: 15px 40px;
                font-size: 1.2rem; border-radius: 50px; cursor: pointer;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                transition: transform 0.2s;
            ">ابدأ الاختبار 🚀</button>
        `;

        content.innerHTML = html;
        this.container.appendChild(content);
        
        document.getElementById('btn-start').onclick = () => {
            // Reset scores cleanly
            this.scores = { total: 0 };
            for(let key in this.totals) {
                if(key !== 'total') this.scores[key] = 0;
            }
            this.startTime = Date.now();
            this.renderQuestion(0);
        };
    }

    renderQuestion(index) {
        if (index >= this.config.questions.length) {
            this.finishGame();
            return;
        }
        this.currentIndex = index;
        const q = this.config.questions[index];

        this.container.innerHTML = ''; 

        const content = document.createElement('div');
        content.style.cssText = "max-width:800px; margin:0 auto; padding:20px; animation: fadeIn 0.5s;";
        
        // Recalculate category safely
        const catSlug = this.normalizeCategory(q.criteria_category);
        const catName = this.getCategoryName(catSlug);
        
        let html = `
            <div style="display:flex; justify-content:space-between; align-items:center; color:#a0aec0; margin-bottom:20px; font-size:0.9rem;">
                <span style="font-weight:bold;">السؤال ${index + 1} / ${this.config.questions.length}</span>
                <span style="background:#edf2f7; padding:6px 15px; border-radius:20px; color:#4a5568; font-weight:bold; font-size:0.85rem;">${catName}</span>
            </div>
        `;

        if(q.audio) {
            html += `<div style="margin-bottom:20px; background:#f7fafc; padding:15px; border-radius:15px; text-align:center;">
                        <p style="margin-bottom:10px; color:#4a5568; font-weight:bold;">🔊 استمع للنص:</p>
                        <audio controls autoplay src="${q.audio}" style="width:100%;"></audio>
                     </div>`;
        }
        if(q.image) {
            html += `<div style="text-align:center; margin-bottom:20px;">
                        <img src="${q.image}" style="max-height:250px; max-width:100%; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                     </div>`;
        }

        html += `<h2 style="color:#2d3748; font-size:1.5rem; margin-bottom:30px; line-height:1.5; text-align:center;">${q.text}</h2>`;

        html += `<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:15px;">`;
        q.answers.forEach((ans, i) => {
            html += `
                <button class="ans-btn" data-idx="${i}" style="
                    padding:15px; background:#fff; border:2px solid #e2e8f0; 
                    border-radius:15px; font-size:1.1rem; cursor:pointer; 
                    color:#4a5568; transition:all 0.2s; font-weight:500;
                    min-height:60px; display:flex; align-items:center; justify-content:center;
                ">${ans.text}</button>`;
        });
        html += `</div>`;

        content.innerHTML = html;
        this.container.appendChild(content);

        content.querySelectorAll('.ans-btn').forEach(btn => {
            btn.onclick = () => this.handleAnswer(q, btn);
        });
    }

    handleAnswer(q, btn) {
        if(btn.disabled) return;
        
        // Lock UI
        const allBtns = this.container.querySelectorAll('.ans-btn');
        allBtns.forEach(b => b.disabled = true);

        const idx = parseInt(btn.dataset.idx);
        const isCorrect = q.answers[idx].correct;
        
        // THE FIX: Re-calculate category right here to be 100% sure
        const cat = this.normalizeCategory(q.criteria_category);

        btn.style.borderColor = isCorrect ? '#48bb78' : '#f56565';
        btn.style.backgroundColor = isCorrect ? '#f0fff4' : '#fff5f5';
        btn.style.color = isCorrect ? '#22543d' : '#822727';
        
        if(!isCorrect) {
            q.answers.forEach((a, i) => {
                if(a.correct) {
                    const correctBtn = this.container.querySelector(`.ans-btn[data-idx="${i}"]`);
                    if(correctBtn) {
                        correctBtn.style.backgroundColor = '#f0fff4';
                        correctBtn.style.borderColor = '#48bb78';
                    }
                }
            });
        }

        // SCORING LOGIC
        if(isCorrect) {
            console.log(`✅ Correct! Adding point to: [${cat}]`);
            this.scores.total++;
            
            if(this.scores[cat] !== undefined) {
                this.scores[cat]++;
            } else {
                console.warn(`⚠️ Warning: Category [${cat}] not found in scoreboard! Adding to 'general'.`);
                if(this.scores.general === undefined) this.scores.general = 0;
                this.scores.general++;
            }
        }

        setTimeout(() => this.renderQuestion(this.currentIndex + 1), 2000);
    }

    finishGame() {
        this.container.innerHTML = '';
        
        // Save
        this.saveProgress();

        const totalPercent = Math.round((this.scores.total / this.totals.total) * 100);
        
        let html = `
            <div style="padding:30px 0; text-align:center;">
                <h1 style="color:#2d3748; margin-bottom:10px;">نتائج الاختبار</h1>
                <div style="font-size:4rem; font-weight:800; color:#667eea; margin:20px 0;">%${totalPercent}</div>
                <p id="save-status" style="color:#718096; font-size:0.9rem; margin-bottom:20px;">جاري حفظ النتيجة...</p>
                
                <div style="background:#f7fafc; border-radius:15px; padding:25px; text-align:right; max-width:600px; margin:0 auto; border:1px solid #edf2f7;">
                    <h3 style="border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:20px; color:#4a5568; font-weight:bold;">📊 تفاصيل المهارات:</h3>
        `;

        // Generate Report
        // Iterate through TOTALS to find which categories exist
        let hasDetails = false;
        
        for (const [cat, totalCount] of Object.entries(this.totals)) {
            if (cat === 'total' || totalCount === 0) continue;
            
            hasDetails = true;
            const score = this.scores[cat] || 0;
            const percent = Math.round((score / totalCount) * 100);
            const color = percent < 50 ? '#f56565' : (percent < 80 ? '#ecc94b' : '#48bb78');
            const catName = this.getCategoryName(cat);

            html += `
                <div style="margin-bottom:15px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-weight:bold; color:#2d3748;">
                        <span>${catName}</span>
                        <span>${score} / ${totalCount}</span>
                    </div>
                    <div style="background:#e2e8f0; height:10px; border-radius:5px; overflow:hidden;">
                        <div style="width:${percent}%; background:${color}; height:100%;"></div>
                    </div>
                </div>`;
        }

        if (!hasDetails) {
            html += `<p style="text-align:center; color:#a0aec0;">⚠️ لا توجد تفاصيل (No Data)</p>`;
        }

        html += `</div>
            
            <div style="margin-top:30px; display:flex; gap:15px; justify-content:center; flex-wrap:wrap;">
                
                <button onclick="location.reload()" style="
                    padding:12px 30px; background:#4a5568; color:white; border:none; 
                    border-radius:50px; cursor:pointer; font-size:1.1rem; display:flex; align-items:center;
                ">
                    🔄 إعادة الاختبار
                </button>

                <button onclick="window.location.href='/exam-results'" style="
                    padding:12px 30px; background:#667eea; color:white; border:none; 
                    border-radius:50px; cursor:pointer; font-size:1.1rem; display:flex; align-items:center;
                    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
                ">
                    📊 الذهاب للنتائج
                </button>

            </div>
        </div>`;
        
        this.container.innerHTML = html;
        console.log("🏁 Game Finished. Final Scores:", this.scores);
    }

    saveProgress() {
        if (typeof sarahLozGame === 'undefined') return;

        const totalPercent = Math.round((this.scores.total / this.totals.total) * 100);
        const timeSpent = Math.floor((Date.now() - this.startTime) / 1000);

        // Prepare breakdown
        const breakdown = {};
        for (const [key, val] of Object.entries(this.totals)) {
            if (val > 0 && key !== 'total') {
                breakdown[key] = {
                    score: this.scores[key] || 0,
                    total: val,
                    percent: Math.round(((this.scores[key] || 0) / val) * 100)
                };
            }
        }

        const data = new FormData();
        data.append('action', 'sarah_loz_track_game_progress');
        data.append('nonce', sarahLozGame.nonce);
        data.append('game_id', sarahLozGame.gameId || this.config.gameId);
        data.append('score', totalPercent);
        data.append('time', timeSpent);
        data.append('completed', 1);
        
        for (const cat in breakdown) {
            data.append(`breakdown[${cat}][score]`, breakdown[cat].score);
            data.append(`breakdown[${cat}][total]`, breakdown[cat].total);
            data.append(`breakdown[${cat}][percent]`, breakdown[cat].percent);
        }

        fetch(sarahLozGame.ajaxUrl, { method: 'POST', body: data })
        .then(response => response.json())
        .then(res => {
            const status = document.getElementById('save-status');
            if(status) {
                status.innerText = "✅ تم حفظ النتيجة بنجاح!";
                status.style.color = "#48bb78";
            }
        })
        .catch(err => {
            const status = document.getElementById('save-status');
            if(status) status.innerText = "❌ حدث خطأ أثناء الحفظ";
        });
    }

    getCategoryName(slug) {
        if (!slug) return 'عام';
        const s = slug.toString().toLowerCase().trim();
        const names = { 'listening': '👂 الاستماع', 'vocabulary': '📖 المفردات', 'grammar': '✍️ القواعد', 'reading': '📚 القراءة', 'fluency': '🗣️ الطلاقة', 'general': 'عام' };
        return names[s] || names[slug] || s;
    }
}
const style = document.createElement('style');
style.innerHTML = `@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }`;
document.head.appendChild(style);