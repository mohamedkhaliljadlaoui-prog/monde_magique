// economy.js - Simple economy management helpers
const Economy = {
	getUser() {
		try { return JSON.parse(localStorage.getItem('user_data')||'{}'); } catch { return {}; }
	},
	saveUser(u) {
		localStorage.setItem('user_data', JSON.stringify(u));
		const diamondsEl = document.getElementById('user-diamonds');
		const coinsEl = document.getElementById('user-coins');
		const xpEl = document.getElementById('user-xp');
		if (diamondsEl) diamondsEl.textContent = u.diamonds||0;
		if (coinsEl) coinsEl.textContent = u.coins||0;
		if (xpEl) xpEl.textContent = u.xp||0;
	},
	deductDiamonds(amount=1) {
		const u = this.getUser();
		if ((u.diamonds||0) < amount) return false;
		u.diamonds -= amount;
		this.saveUser(u);
		return true;
	},
	addCoins(amount=0) { const u=this.getUser(); u.coins=(u.coins||0)+amount; this.saveUser(u); },
	addXP(amount=0) { const u=this.getUser(); u.xp=(u.xp||0)+amount; this.saveUser(u); }
};

if (typeof window !== 'undefined') window.Economy = Economy;
