/**
 * Reactive state management for the Admin dashboard.
 */
class State {
    constructor() {
        this._students = [];
        this._filterType = 'ALL';
        this._metrics = {};
        this._subscribers = {
            'students': [],
            'filterType': [],
            'metrics': []
        };
    }

    // Getters
    get students() { return [...this._students]; }
    get filterType() { return this._filterType; }
    get metrics() { return { ...this._metrics }; }

    // Setters with notification
    setStudents(data) {
        this._students = data;
        this._notify('students', data);
    }

    setFilterType(type) {
        this._filterType = type;
        this._notify('filterType', type);
    }

    setMetrics(data) {
        this._metrics = data;
        this._notify('metrics', data);
    }

    // Subscription system
    subscribe(event, callback) {
        if (this._subscribers[event]) {
            this._subscribers[event].push(callback);
        }
    }

    _notify(event, data) {
        if (this._subscribers[event]) {
            this._subscribers[event].forEach(cb => cb(data));
        }
    }
}

export const state = new State();
