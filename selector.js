export default class Selector {
    static MAIN_ATTRIBUTE = "data-main-selector-id";
    static ATTRIBUTE = "data-selector-id";

    get main() {
        return this._main;
    }

    get subs() {
        return this._subs;
    }

    get selected() {
        return this.subs.filter((e) => $(e).prop("checked"));
    }
    get deselected() {
        return this.subs.filter((e) => !$(e).prop("checked"));
    }

    get selectedValues() {
        return this.selected.map((e) => $(e).attr('value'));
    }

    get deselectedValues() {
        return this.deselected.map((e) => $(e).attr('value'));
    }

    constructor(dataId) {
        this.dataId = dataId;
        this.initialize();
    }

    initialize(callback=null) {
        this._main = $(
            `input[type=checkbox][${Selector.MAIN_ATTRIBUTE}='${this.dataId}']`).toArray();
        this._subs = $(
            `input[type=checkbox][${Selector.ATTRIBUTE}='${this.dataId}']`).toArray();
        this._selected = [];
        
        const main = $(this.main);
        main.off("change");
        main.change((ev) => {
            if(ev.target.checked) this.selectAll();
            else this.deselectAll();
        });
        // main.trigger("change");

        const subs = $(this.subs);
        subs.off("change");
        subs.change((ev) => {
            if(this.selected.length == this.subs.length) main.prop("checked", true);
            else main.prop("checked", false);
            if(callback) callback(ev);
        });
        subs.trigger("change");
    }

    select(element) {
        element = $(element);
        if(!element.prop("checked")) {
            element.prop("checked", true);
            element.trigger("change");
        }
        if(this.deselected.length == 0) $(this.main).prop("checked", true);
    }

    deselect(element) {
        element = $(element);
        if(element.prop("checked")) {
            element.prop("checked", false);
            element.trigger("change");
        }
        $(this.main).prop("checked", false);
    }

    invert(element) {
        element = $(element);
        if(element.prop("checked")) this.deselect(element);
        else this.select(element);
        element.trigger("change");
    }

    selectAll() {
        const self = this;
        $(this.subs).each(function() {self.select(this)});
    }

    deselectAll() {
        const self = this;
        $(this.subs).each(function() {self.deselect(this)});
    }

    invertSelection() {
        const self = this;
        $(this.subs).each(function() {self.invert(this)});
    }
}