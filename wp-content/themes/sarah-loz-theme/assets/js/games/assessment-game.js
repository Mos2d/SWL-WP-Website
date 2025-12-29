console.log('🔌 Phase 19: Content Slides (No-Answer Logic)');

class AssessmentGame {
    constructor(containerId, config) {
        this.container = document.getElementById(containerId);
        this.config = config;
        this.currentIndex = 0;
        this.startTime = Date.now();
        
        // 1. Initialize Scoreboard
        this.scores = { total: 0 };
        this.totals = { total: 0 };
        
        // 2. Scan questions to build the "Totals"
        this.config.questions.forEach((q, index) => {
            const cat = this.normalizeCategory(q.criteria_category);
            
            if (this.totals[cat] === undefined) {
                this.totals[cat] = 0;
                this.scores[cat] = 0;
            }
            this.totals[cat]++;
            
            // Only exclude 'none' from the total count
            if (cat !== 'none') {
                this.totals.total++;
            }
        });
    }

    normalizeCategory(raw) {
        if (!raw) return 'general';
        return raw.toString().toLowerCase().trim();
    }

    init() {
        if (!this.container) return;

        // Reset Container
        this.container.innerHTML = '';
        this.container.style.display = 'block';
        this.container.style.minHeight = '650px'; 
        this.container.style.height = '100%'; 

        // Remove old loaders
        const oldLoader = document.getElementById('game-loader');
        if(oldLoader) oldLoader.remove();
        document.querySelectorAll('.game-loader').forEach(l => l.remove());

        this.renderStartScreen();
    }

    renderStartScreen() {
        const content = document.createElement('div');
        content.style.cssText = "display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:500px; height:100%; text-align:center; padding: 20px;";
        
        const totalQs = this.totals.total || this.config.questions.length;

        let html = `
            <div style="margin-bottom:30px;">
                <h1 style="color:#2d3748; font-size: 2rem; margin-bottom: 15px;">📝 اختبار تحديد المستوى</h1>
                <p style="color:#718096; font-size: 1.1rem;">عدد الأسئلة: <strong>${totalQs}</strong></p>
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
        
        // Reset Selection State
        this.selectedAnswerIndex = null;
        this.selectedAnswerBtn = null;
        // Reset Rearrange State
        this.rearrangeState = null; 

        const q = this.config.questions[index];
        
        // ✅ FIX: Define qType here so it can be used later
        const qType = q.type || 'multiple_choice'; 

        // ✅ FIX: Initialize Rearrange Logic if needed
        if (qType === 'rearrange') {
            const items = q.answers.map((a, i) => ({ ...a, origIndex: i }));
            // Shuffle
            for (let i = items.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [items[i], items[j]] = [items[j], items[i]];
            }
            this.rearrangeState = { pool: items, chain: [] };
        }

        this.container.innerHTML = ''; 

        const content = document.createElement('div');
        content.style.cssText = "display:flex; flex-direction:column; justify-content:space-between; min-height:600px; height:100%; max-width:900px; margin:0 auto; padding:20px 0; animation: fadeIn 0.3s;";
        
        const catSlug = this.normalizeCategory(q.criteria_category);
        const catName = this.getCategoryName(catSlug);
        const displayCat = (catSlug === 'none') ? '' : catName;
        
        // CHECK: Is this a content-only slide?
        const isContentSlide = (catSlug === 'none');

        // --- TOP: Header ---
        let html = `
            <div style="display:flex; justify-content:space-between; align-items:center; color:#a0aec0; margin-bottom:10px; font-size:0.9rem;">
                <span style="font-weight:bold;">${isContentSlide ? '📄 معلومة' : `السؤال ${index + 1}`}</span>
                ${displayCat ? `<span style="background:#edf2f7; padding:6px 15px; border-radius:20px; color:#4a5568; font-weight:bold; font-size:0.85rem;">${displayCat}</span>` : ''}
            </div>
        `;

        // --- MIDDLE: Content ---
        html += `<div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; margin: 10px 0;">`;
        
        if(q.audio) {
            html += `<div style="margin-bottom:20px; background:#f7fafc; padding:15px; border-radius:15px; width:100%; max-width:400px;">
                        <p style="margin-bottom:10px; color:#4a5568; font-weight:bold;">🔊 استمع للنص:</p>
                        <audio controls autoplay src="${q.audio}" style="width:100%;"></audio>
                     </div>`;
        }
        
        if(q.image) {
            const cursorStyle = q.click_audio ? 'cursor:pointer; transform:scale(1); transition:transform 0.2s;' : '';
            html += `<div style="margin-bottom:20px; width:100%; text-align:center;">
                        <img id="q-interaction-img" 
                             src="${q.image}" 
                             onmouseover="this.style.transform='scale(1.02)'" 
                             onmouseout="this.style.transform='scale(1)'"
                             style="max-height:350px; max-width:100%; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:inline-block; ${cursorStyle}">
                     </div>`;
        }

        const textCursor = q.click_audio ? 'cursor:pointer; color:#2b6cb0; transition:color 0.2s;' : 'color:#2d3748;';
        html += `<h2 id="q-interaction-text" 
                     style="${textCursor} font-size:1.8rem; line-height:1.4; margin-top:10px;">
                     ${q.text} ${q.click_audio ? '🔊' : ''}
                 </h2>`;
                 
        html += `</div>`; 

        content.innerHTML = html;

        // --- INTERACTION AREA ---
        if (!isContentSlide) {
            if (qType === 'rearrange') {
                // CALL NEW HELPER METHOD
                this.renderRearrangeArea(content);
            } else if (q.answers && q.answers.length > 0) {
                // STANDARD MULTIPLE CHOICE LOGIC
                const choicesContainer = document.createElement('div');
                choicesContainer.style.cssText = "display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:15px; width:100%;";
                
                q.answers.forEach((ans, i) => {
                    const btn = document.createElement('button');
                    btn.className = 'ans-btn';
                    btn.dataset.idx = i;
                    
                    let innerContent = '';
                    let btnStyle = "padding:15px; background:#fff; border:2px solid #e2e8f0; border-radius:15px; font-size:1.1rem; cursor:pointer; color:#4a5568; transition:all 0.2s; font-weight:500; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;";
                    
                    if(ans.image && ans.image.length > 5) {
                        innerContent += `<img src="${ans.image}" style="height:120px; width:auto; object-fit:contain; margin-bottom:10px; border-radius:8px;">`;
                        btnStyle += " min-height:160px;"; 
                    } else {
                        btnStyle += " min-height:80px;"; 
                    }
                    if(ans.text) innerContent += `<span>${ans.text}</span>`;
                    
                    btn.innerHTML = innerContent;
                    btn.style.cssText = btnStyle;
                    
                    btn.onclick = () => {
                        if(ans.audio) new Audio(ans.audio).play().catch(()=>{});
                        this.selectAnswer(btn);
                    };
                    choicesContainer.appendChild(btn);
                });
                content.appendChild(choicesContainer);
            }
        }

        // --- FOOTER: Action Button (Dynamic) ---
        const btnText = isContentSlide ? 'استمرار ⬅️' : 'تأكيد الإجابة <span style="font-size:1.2rem;">✅</span>';
        const btnState = isContentSlide ? '' : 'disabled';
        const btnCursor = isContentSlide ? 'cursor:pointer' : 'cursor:not-allowed';
        const btnBg = isContentSlide ? 'background: linear-gradient(135deg, #48bb78 0%, #38a169 100%)' : 'background: #cbd5e0';
        const btnShadow = isContentSlide ? 'box-shadow: 0 4px 15px rgba(72, 187, 120, 0.4)' : '';

        const footerDiv = document.createElement('div');
        footerDiv.style.cssText = "display:flex; justify-content:flex-start; margin-top:30px; width:100%; border-top:1px solid #edf2f7; padding-top:20px;";
        
        footerDiv.innerHTML = `
            <button id="btn-next-confirm" ${btnState} style="
                ${btnBg}; color: white; border: none; padding: 12px 40px;
                font-size: 1.1rem; border-radius: 50px; ${btnCursor};
                transition: all 0.3s; display: flex; align-items: center; gap: 10px; font-weight: bold;
                ${btnShadow};
            ">
                ${btnText}
            </button>
        `;

        content.appendChild(footerDiv);
        this.container.appendChild(content);

        // --- EVENT LISTENERS ---
        const playQAudio = () => {
            if (q.click_audio) {
                new Audio(q.click_audio).play().catch(e => console.error("Audio Error:", e));
            }
        };

        const imgEl = document.getElementById('q-interaction-img');
        if(imgEl && q.click_audio) imgEl.addEventListener('click', playQAudio);

        const textEl = document.getElementById('q-interaction-text');
        if(textEl && q.click_audio) textEl.addEventListener('click', playQAudio);

        // 1. Answer Selection Handler (Only if buttons exist - Fixed for Rearrange safety)
        if (qType !== 'rearrange') {
            content.querySelectorAll('.ans-btn').forEach(btn => {
                btn.onclick = () => {
                    const ans = q.answers[btn.dataset.idx];
                    if(ans.audio) {
                        new Audio(ans.audio).play().catch(e => console.error("Answer Audio Error:", e));
                    }
                    this.selectAnswer(btn);
                };
            });
        }

        // 2. Confirm/Next Button Handler
        const confirmBtn = document.getElementById('btn-next-confirm');
        confirmBtn.onclick = () => {
            if (isContentSlide) {
                this.renderQuestion(this.currentIndex + 1);
            } else {
                if(qType === 'rearrange') {
                     this.handleAnswerSubmission(q);
                } else if(this.selectedAnswerBtn) {
                     this.handleAnswerSubmission(q, this.selectedAnswerBtn);
                }
            }
        };
        
        // 3. Initialize Rearrange DOM if needed
        if (qType === 'rearrange' && !isContentSlide) {
            this.refreshRearrangeDOM();
        }
    }

    selectAnswer(btn) {
        this.container.querySelectorAll('.ans-btn').forEach(b => {
            b.style.borderColor = '#e2e8f0';
            b.style.backgroundColor = '#fff';
            b.style.transform = 'scale(1)';
            b.style.boxShadow = 'none';
        });

        btn.style.borderColor = '#667eea';
        btn.style.backgroundColor = '#ebf4ff'; 
        btn.style.transform = 'scale(1.02)';
        btn.style.boxShadow = '0 4px 12px rgba(102, 126, 234, 0.2)';

        this.selectedAnswerBtn = btn;
        const confirmBtn = document.getElementById('btn-next-confirm');
        if(confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            confirmBtn.style.cursor = 'pointer';
            confirmBtn.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.4)';
        }
    }

    handleAnswerSubmission(q, btn) {
        const confirmBtn = document.getElementById('btn-next-confirm');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = 'جاري التحقق...';

        // --- 1. HANDLE REARRANGE LOGIC ---
        if (q.type === 'rearrange') {
            // Check if the user's chain order matches the original index order (0, 1, 2...)
            // We assume the original 'q.answers' array was in the correct order.
            const isCorrect = this.rearrangeState.chain.every((item, index) => item.origIndex === index);
            
            // Visual Feedback for the Chain Container
            const chainZone = document.getElementById('rearrange-chain');
            if (chainZone) {
                chainZone.style.border = isCorrect ? '2px solid #48bb78' : '2px solid #f56565';
                chainZone.style.backgroundColor = isCorrect ? '#f0fff4' : '#fff5f5';
            }

            // Update Score
            this.processScore(q, isCorrect);

            // Move to next question after delay
            setTimeout(() => this.renderQuestion(this.currentIndex + 1), 1500);
            return; 
        }

        // --- 2. HANDLE MULTIPLE CHOICE LOGIC ---
        // (Only runs if not rearrange)
        
        // Disable all option buttons
        const allBtns = this.container.querySelectorAll('.ans-btn');
        allBtns.forEach(b => b.disabled = true);

        // Safety check: if no button was passed (shouldn't happen for multiple choice), stop here
        if (!btn) return;

        const idx = parseInt(btn.dataset.idx);
        const isCorrect = q.answers[idx].correct;
        
        // Visual Feedback for Buttons
        btn.style.borderColor = isCorrect ? '#48bb78' : '#f56565';
        btn.style.backgroundColor = isCorrect ? '#f0fff4' : '#fff5f5';
        btn.style.color = isCorrect ? '#22543d' : '#822727';
        
        if(!isCorrect) {
            // Highlight the correct one if user was wrong
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

        // Update Score
        this.processScore(q, isCorrect);

        setTimeout(() => this.renderQuestion(this.currentIndex + 1), 1000);
    }

    // Helper to avoid duplicating scoring logic
    processScore(q, isCorrect) {
        if(isCorrect) {
            const cat = this.normalizeCategory(q.criteria_category);
            
            if (cat !== 'none') {
                this.scores.total++;
            }
            
            if(this.scores[cat] !== undefined) {
                this.scores[cat]++;
            } else {
                if(this.scores.general === undefined) this.scores.general = 0;
                this.scores.general++;
            }
        }
    }

    finishGame() {
        this.container.innerHTML = '';
        this.saveProgress();

        const totalPossible = this.totals.total || 1;
        const totalPercent = Math.round((this.scores.total / totalPossible) * 100);
        
        let html = `
            <div style="padding:30px 0; text-align:center;">
                <h1 style="color:#2d3748; margin-bottom:10px;">نتائج الاختبار</h1>
                <div style="font-size:4rem; font-weight:800; color:#667eea; margin:20px 0;">%${totalPercent}</div>
                <p id="save-status" style="color:#718096; font-size:0.9rem; margin-bottom:20px;">جاري حفظ النتيجة...</p>
                
                <div style="background:#f7fafc; border-radius:15px; padding:25px; text-align:right; max-width:600px; margin:0 auto; border:1px solid #edf2f7;">
                    <h3 style="border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:20px; color:#4a5568; font-weight:bold;">📊 المهارات الفرعية:</h3>
        `;

        let hasDetails = false;
        
        for (const [cat, totalCount] of Object.entries(this.totals)) {
            if (cat === 'total' || cat === 'none' || totalCount === 0) continue;
            
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

        const totalPossible = this.totals.total || 1;
        const totalPercent = Math.round((this.scores.total / totalPossible) * 100);
        const timeSpent = Math.floor((Date.now() - this.startTime) / 1000);

        const breakdown = {};
        for (const [key, val] of Object.entries(this.totals)) {
            if (val > 0 && key !== 'total' && key !== 'none') {
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
        
        const names = { 
            'general_meaning': 'يحدد المعنى العام حتى لو لم يفهم جميع التفاصيل',
            'specific_info': 'يستخلص معلومات محددة من نص مسموع',
            'true_false': 'يميز المعلومة الصحيحة من المعلومة الخاطئة في نص مسموع',
            'common_phrases': 'يميز العبارات الشائعة والمحفوظة التي تظهر في مواقف التواصل الأساسية',
            'vocab_meaning': 'يربط المفردات المسموعة بمدلولها',
            'sequence_events': 'يتتبع تسلسل أحداث بسيطة في قصة أو حوار قصير مسموع',
            'form_opinion': 'يكون رأيا فيما يسمع',
            'none': 'غير محتسب',
        };
        
        return names[s] || s;
    }
    renderRearrangeArea(contentElement) {
        const areaContainer = document.createElement('div');
        areaContainer.style.width = '100%';
        areaContainer.style.margin = '10px 0';
        
        // 1. Lower Level (User's Chain)
        const lowerLabel = document.createElement('div');
        lowerLabel.innerText = 'ترتيبك (اضغط للإلغاء):';
        lowerLabel.style.cssText = 'color:#718096; font-size:0.9rem; margin-bottom:5px; text-align:right; width:100%; font-weight:bold;';
        areaContainer.appendChild(lowerLabel);

        const lowerZone = document.createElement('div');
        lowerZone.id = 'rearrange-chain';
        lowerZone.style.cssText = `
            display:flex; flex-wrap:wrap; gap:10px; min-height:90px; width:100%;
            background:#f7fafc; border:2px dashed #cbd5e0; border-radius:12px;
            padding:15px; align-items:center; justify-content:flex-start; direction: rtl;
        `;
        areaContainer.appendChild(lowerZone);

        // 2. Upper Level (Options Pool)
        const upperLabel = document.createElement('div');
        upperLabel.innerText = 'الخيارات (اضغط للاختيار):';
        upperLabel.style.cssText = 'color:#718096; font-size:0.9rem; margin:25px 0 5px 0; text-align:right; width:100%; font-weight:bold;';
        areaContainer.appendChild(upperLabel);

        const upperZone = document.createElement('div');
        upperZone.id = 'rearrange-pool';
        upperZone.style.cssText = `display:flex; flex-wrap:wrap; gap:10px; width:100%; justify-content:center; padding:10px 0; direction: rtl;`;
        areaContainer.appendChild(upperZone);

        contentElement.appendChild(areaContainer);
    }

    createRearrangeBtn(item, source, index) {
        const btn = document.createElement('button');
        let label = item.text || '';
        
        if(item.image) {
             label = `<img src="${item.image}" style="height:50px; vertical-align:middle; border-radius:4px; margin-left:8px;"> ` + label;
        }

        btn.innerHTML = label;
        btn.style.cssText = `
            padding: 8px 20px; background: white; border: 2px solid #e2e8f0;
            border-radius: 10px; font-size: 1rem; cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.05); color: #2d3748;
            transition: all 0.2s; display:flex; align-items:center; font-weight:600;
        `;
        
        if (source === 'chain') {
             btn.style.background = '#ebf8ff';
             btn.style.borderColor = '#4299e1';
             btn.style.color = '#2b6cb0';
        }

        btn.onclick = () => {
             if(item.audio) new Audio(item.audio).play().catch(()=>{});
             this.handleRearrangeMove(source, index);
        };
        return btn;
    }

    handleRearrangeMove(source, index) {
        if(document.getElementById('btn-next-confirm').innerText.includes('تحقق')) return;

        if (source === 'pool') {
            const item = this.rearrangeState.pool.splice(index, 1)[0];
            this.rearrangeState.chain.push(item);
        } else {
            const item = this.rearrangeState.chain.splice(index, 1)[0];
            this.rearrangeState.pool.push(item);
        }
        this.refreshRearrangeDOM();
    }

    refreshRearrangeDOM() {
        const lowerZone = document.getElementById('rearrange-chain');
        const upperZone = document.getElementById('rearrange-pool');
        const confirmBtn = document.getElementById('btn-next-confirm');
        
        if(!lowerZone || !upperZone) return; 

        lowerZone.innerHTML = '';
        this.rearrangeState.chain.forEach((item, idx) => {
            lowerZone.appendChild(this.createRearrangeBtn(item, 'chain', idx));
        });

        upperZone.innerHTML = '';
        this.rearrangeState.pool.forEach((item, idx) => {
            upperZone.appendChild(this.createRearrangeBtn(item, 'pool', idx));
        });

        // Only enable 'Confirm' if all items are used
        const isComplete = (this.rearrangeState.pool.length === 0);
        
        if(confirmBtn) {
             if (isComplete) {
                 confirmBtn.disabled = false;
                 confirmBtn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                 confirmBtn.style.cursor = 'pointer';
             } else {
                 confirmBtn.disabled = true;
                 confirmBtn.style.background = '#cbd5e0';
                 confirmBtn.style.cursor = 'not-allowed';
             }
        }
    }
}
const style = document.createElement('style');
style.innerHTML = `@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }`;
document.head.appendChild(style);