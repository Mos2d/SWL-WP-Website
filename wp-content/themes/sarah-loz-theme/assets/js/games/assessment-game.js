console.log('🔌 Phase 10: Theme Integration Loaded');

class AssessmentGame {
    constructor(containerId, config) {
        this.container = document.getElementById(containerId);
        this.config = config;
        this.currentIndex = 0;
        
        // Initialize Scores
        this.scores = { total: 0, listening: 0, vocabulary: 0, grammar: 0, reading: 0, fluency: 0, general: 0 };
        this.totals = { total: this.config.questions.length, listening: 0, vocabulary: 0, grammar: 0, reading: 0, fluency: 0, general: 0 };

        // Count Categories
        this.config.questions.forEach(q => {
            const cat = (q.criteria_category || 'general').toLowerCase(); 
            if (this.totals[cat] !== undefined) this.totals[cat]++;
            else this.totals.general++;
        });
    }

    init() {
        if (!this.container) {
            console.error("❌ Game Container not found. Looking for:", this.container);
            return;
        }

        console.log("✅ Container Found. Clearing Theme Loader...");

        // 1. CLEAR CONTAINER (This removes the theme's spinner)
        this.container.innerHTML = '';
        
        // 2. Reset Styles to match theme expectations
        this.container.style.display = 'block';
        this.container.style.opacity = '1';
        this.container.style.visibility = 'visible';
        this.container.style.height = 'auto';
        this.container.style.minHeight = '600px';

        // 3. Render
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
        
        document.getElementById('btn-start').onclick = () => this.renderQuestion(0);
    }

    renderQuestion(index) {
        if (index >= this.config.questions.length) {
            this.finishGame();
            return;
        }
        this.currentIndex = index;
        const q = this.config.questions[index];

        this.container.innerHTML = ''; // Clear previous content

        const content = document.createElement('div');
        content.style.cssText = "max-width:800px; margin:0 auto; padding:20px; animation: fadeIn 0.5s;";
        
        // Header
        const catSlug = q.criteria_category || 'general';
        const catName = this.getCategoryName(catSlug);
        
        let html = `
            <div style="display:flex; justify-content:space-between; align-items:center; color:#a0aec0; margin-bottom:20px; font-size:0.9rem;">
                <span style="font-weight:bold;">السؤال ${index + 1} / ${this.config.questions.length}</span>
                <span style="background:#edf2f7; padding:6px 15px; border-radius:20px; color:#4a5568; font-weight:bold; font-size:0.85rem;">${catName}</span>
            </div>
        `;

        // Content
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

        // Answers
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
        const allBtns = this.container.querySelectorAll('.ans-btn');
        allBtns.forEach(b => b.disabled = true);

        const idx = parseInt(btn.dataset.idx);
        const isCorrect = q.answers[idx].correct;
        const cat = (q.criteria_category || 'general').toLowerCase();

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

        if(isCorrect) {
            this.scores.total++;
            if(this.scores[cat] !== undefined) this.scores[cat]++;
            else this.scores.general++;
        }

        setTimeout(() => this.renderQuestion(this.currentIndex + 1), 2000);
    }

    finishGame() {
        this.container.innerHTML = '';
        const totalPercent = Math.round((this.scores.total / this.totals.total) * 100);
        
        let html = `
            <div style="padding:30px 0; text-align:center;">
                <h1 style="color:#2d3748; margin-bottom:10px;">نتائج الاختبار</h1>
                <div style="font-size:4rem; font-weight:800; color:#667eea; margin:20px 0;">%${totalPercent}</div>
                <div style="background:#f7fafc; border-radius:15px; padding:25px; text-align:right; max-width:600px; margin:0 auto; border:1px solid #edf2f7;">
                    <h3 style="border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:20px; color:#4a5568; font-weight:bold;">📊 تفاصيل المهارات:</h3>
        `;

        const categories = ['listening', 'vocabulary', 'grammar', 'reading', 'fluency', 'general'];
        let hasDetails = false;

        categories.forEach(cat => {
            if (this.totals[cat] > 0) {
                hasDetails = true;
                const percent = Math.round((this.scores[cat] / this.totals[cat]) * 100);
                const color = percent < 50 ? '#f56565' : (percent < 80 ? '#ecc94b' : '#48bb78');
                
                html += `
                    <div style="margin-bottom:15px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-weight:bold; color:#2d3748;">
                            <span>${this.getCategoryName(cat)}</span>
                            <span>${this.scores[cat]} / ${this.totals[cat]}</span>
                        </div>
                        <div style="background:#e2e8f0; height:10px; border-radius:5px; overflow:hidden;">
                            <div style="width:${percent}%; background:${color}; height:100%;"></div>
                        </div>
                    </div>`;
            }
        });

        if (!hasDetails) {
            html += `<p style="text-align:center; color:#a0aec0;">⚠️ لا توجد تفاصيل (No Data)</p>`;
        }

        html += `</div>
                <button onclick="location.reload()" style="margin-top:30px; padding:12px 30px; background:#4a5568; color:white; border:none; border-radius:50px; cursor:pointer;">🔄 إعادة الاختبار</button>
            </div>`;
        
        this.container.innerHTML = html;
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