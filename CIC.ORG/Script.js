document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // --- Mobile Menu Toggle ---
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenuButton.classList.toggle('open');
        });
    }

    // --- Toast Notification ---
    const toast = document.getElementById('toast');
    window.showToast = function(message, isSuccess = true) {
        if (toast) {
            toast.textContent = message;
            toast.className = `fixed bottom-5 right-5 text-white py-2 px-4 rounded-lg shadow-lg transition-opacity duration-300 ${isSuccess ? 'bg-green-500' : 'bg-red-500'}`;
            toast.classList.remove('opacity-0');
            setTimeout(() => {
                toast.classList.add('opacity-0');
            }, 3000);
        }
    }

    // --- Form Submissions ---
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            const data = {};
            for (const [k, v] of fd.entries()) data[k] = v;
            try {
                const resp = await fetch('/api/contact.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
                const json = await resp.json().catch(() => null);
                if (resp.ok && json && json.success) {
                    window.showToast('Message sent successfully!');
                    this.reset();
                } else {
                    window.showToast((json && json.error) ? json.error : 'Failed to send message', false);
                }
            } catch (err) {
                window.showToast('Network error: could not reach API', false);
            }
        });
    }

    // --- Newsletter Subscribe ---
    const subscribeForm = document.getElementById('subscribeForm');
    if (subscribeForm) {
        subscribeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const emailEl = document.getElementById('subscribeEmail');
            const email = emailEl ? emailEl.value.trim() : '';
            if (!email) return;
            try {
                const resp = await fetch('/api/subscribe.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ email }) });
                const json = await resp.json().catch(() => null);
                if (resp.ok && json && json.success) {
                    window.showToast(json.message || 'Subscribed!', true);
                    this.reset();
                } else {
                    window.showToast((json && json.error) ? json.error : 'Subscription failed', false);
                }
            } catch (err) {
                window.showToast('Network error: could not reach API', false);
            }
        });
    }

    const jobApplicationForm = document.getElementById('jobApplicationForm');
    if (jobApplicationForm) {
        jobApplicationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            const data = {};
            for (const [k, v] of fd.entries()) data[k] = v;
            try {
                const resp = await fetch('/api/apply.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
                const json = await resp.json().catch(() => null);
                if (resp.ok && json && json.success) {
                    window.showToast('Application submitted successfully!');
                    this.reset();
                    const container = document.getElementById('application-form-container');
                    if (container) container.classList.add('hidden');
                } else {
                    window.showToast((json && json.error) ? json.error : 'Failed to submit application', false);
                }
            } catch (err) {
                window.showToast('Network error: could not reach API', false);
            }
        });
    }

    // --- Career Application Form Toggle ---
    const applicationFormContainer = document.getElementById('application-form-container');
    window.showApplicationForm = function() {
        if(applicationFormContainer){
            applicationFormContainer.classList.remove('hidden');
            applicationFormContainer.scrollIntoView({ behavior: 'smooth' });
        }
    }
});
