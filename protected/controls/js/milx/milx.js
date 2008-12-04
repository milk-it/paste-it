Event.onLoad = function(act) {
    Event.observe(window, 'load', act);
};

var Milx = {
    Version: '0.1',

    Exception: function(msg) {
        alert(msg);
    },

    InvalidStatementException: function(st, err) {
        this.Exception("Invalid statement: " + st + "\n" + err);
    },

    Params: $H(document.URL.toQueryParams())
}

Milx.FormHelper = {
  getTag: function(type, name, value) {
    a = document.createElement("input");
    a.type = type;
    a.name = name;
    a.value = value;

    return a;
  }
}

Milx.FormHelper.ActionCombo = Class.create();
Milx.FormHelper.ActionCombo.prototype = {
    initialize: function(el, defaultIndex) {
        this._callBacks    = [];
        this._defaultIndex = defaultIndex;
        this.el            = $(el);
        Event.observe(this.el, 'change', this._comboChangeAction.bindAsEventListener(this));
    },

    _comboChangeAction: function(e) {
        value = $F(this.el);
        if (!(callback = this._callBacks[value]))
            callback = value;

        if (typeof(callback) == "function")
            callback();
        else
            try { eval(callback); } catch(e) { Milx.InvalidStatementException(callback, e); }
        
        if (this._defaultIndex != undefined)
            this.el.selectedIndex = this._defaultIndex;
    },

    addAction: function(desc, callback, createOption) {
        createOption = createOption || false;
        if (!this._callBacks[desc] && createOption) {
            node = document.createElement("option");
            node.innerHTML = desc;
            this.el.appendChild(node);
        }
        this._callBacks[desc] = callback;
    }
}

Milx.FormHelper.AutoResizeTextarea = Class.create();
Milx.FormHelper.AutoResizeTextarea.prototype = {
    initialize: function(el, maxRows, minRows) {
        this.el = $(el);
        this._minRows = minRows || this.el.rows;
        this._maxRows = maxRows || false;
        this._resizeEvent = this._adaptRows.bindAsEventListener(this);
        Event.observe(this.el, "keyup", this._resizeEvent);
        this.start();
    },

    _adaptRows: function() {
        if (this._doResize) {
            lines = $A(this.el.value.split("\n")).length;
            alines = this.el.rows;

            if (alines > this.maxRows || (lines > alines && this._maxRows && lines > this._maxRows))
                this.el.rows = this._maxRows;
            else if (alines < this.minRows || (alines > lines && lines < this._minRows))
                this.el.rows = this._minRows;
            else
                this.el.rows = lines;
        }
    },

    isStarted: function() {
        return this._doResize;
    },

    stop: function() {
        this._doResize = false;
    },

    start: function() {
        this._doResize = true;
        this._adaptRows();
    }
}

Milx.FormHelper.ResizableTextArea = Class.create({
    initialize: function(el, options)
    {
        options = options || {};
        this.grippie = new Element("div", options.grippie || {
            class: "grippie",
            style: "cursor:s-resize;height:5px;background-color:#EEEEEE;border:#DDDDDD solid;border-width:0pt 1px 1px"
        });
        this.el = $(el);
        this.el.wrap().insert(this.grippie);
        this.grippie.observe("mousedown", startDrag.bindAsEventListener(this));
        this.changeOpacity = options.changeOpacity || 0.25;

        function startDrag(ev)
        {
            staticOffset = this.el.getHeight() - ev.pointerY();
            this.el.setStyle({opacity:this.changeOpacity});
            this.performDragEvent = performDrag.bindAsEventListener(this, staticOffset);
            this.endDragEvent = endDrag.bindAsEventListener(this);
            $(document).observe("mousemove", this.performDragEvent);
            $(document).observe("mouseup", this.endDragEvent);
        }

        function performDrag(ev, staticOffset)
        {
            this.el.setStyle({height: Math.max(32, staticOffset + ev.pointerY()) + 'px'});
            ev.stop();
        }

        function endDrag(ev)
        {
            $(document).stopObserving("mousemove", this.performDragEvent);
            $(document).stopObserving("mouseup", this.endDragEvent);
            this.el.setStyle({opacity: 1});
        }
    }
});

Milx.Table = Class.create();
Milx.Table.prototype = {
    initialize: function(tableEl, options) {
        options = options || {};
        this.el = $(tableEl);
        this.checkboxPrefix = options.checkboxPrefix || "id";
        this.__cssSelector = 'input[type="checkbox"][name="' + this.checkboxPrefix + '[]"]';
    },
    
    getSelectedCheckboxes: function() {
        selecteds = [];
        this.el.getElementsBySelector(this.__cssSelector).each(function(e) {
            if (e.checked) selecteds.push(e);
        });
        return selecteds;
    }
}

Milx.Table.OrderBy = Class.create();
Milx.Table.OrderBy.prototype = {
    _isDesc: function(nick) {
        return ((Milx.Params["filter[order][field]"]==nick) && (Milx.Params["filter[order][type]"]=="desc")) ? true : false;
    }, 

    initialize: function(table, nicks) {
        this.table = $(table);
        this.nicks = nicks || {};
        ths = $A(this.table.getElementsByTagName("th"));
        ths.invoke("setStyle", {cursor: 'pointer'});
        ths.invoke("observe", "click", function(e) { 
            e = Event.element(e).innerHTML.toLowerCase();
            var nick = this.nicks[e] || e;
            window.location = "?"+Milx.Params.merge({"filter[order][field]": nick, "filter[order][type]": (this._isDesc(nick)) ? "asc" : "desc"}).toQueryString();
        }.bindAsEventListener(this));
    } 
}

Milx.Table.Filter = Class.create();
Milx.Table.Filter.prototype = {
    /**
     * This class manage the filter table
     * @param {String|Object} table Table that show the filters
     * @param {String|Object} select The selectbox that add a filter in filter table
     * @constructor
     * @see Milx.FormHelper.ActionCombo
     */
    initialize: function(table, select) {
        this.table = $(table);
        this.select = new Milx.FormHelper.ActionCombo(select, 0);
        this.options = {};

        // Add new blank option
        this.select.addAction("", function() {}, true);
    },

    /**
     * Set option to the select of Add Filter
     * The options variable is on format:
     * {id: {type:, label:, value:} [, {id1:...] }
     * 
     * The id order is reserved!
     *
     * @param {Hash} options The options to the select
     */
    setOptions: function(options) {
        this.options = $H(options);
        this.options.each(function(option) {
            this.select.addAction(
                option.value.label, 
                this._addFilter.bind(this, option.key, option.value.label, option.value.type, option.value.value),
                true
            );
        }.bindAsEventListener(this));
    },

    /**
     * Add a filter in of table.
     * Exist two types of field:
     * select = Selectbox
     * text = Textfield
     * special = HTML Tag
     */
    _addFilter: function(id, label, type, values) {
        // Create row
        var row = this.table.insertRow(0);
        $(row.insertCell(0)).update(label);

        // Create the text or select
        switch (type) {
            case "text":
                $(row.insertCell(1)).update('<input type="text" name="filter['+id+']" value="'+values+'" />');
                break;
            case "select":
                var select = document.createElement("select");
                select.name = "filter["+id+"]";

                $A(values).each(function(value) {
                    var option = document.createElement("option");
                    option.text = value[1];
                    option.value = value[0];
                    select.add(option, null);
                });

                row.insertCell(1).appendChild(select);
                break;
            case "special":
                $(row.insertCell(1)).update(values);
                break;
        }

        // Configuring the button
        var btn = $(document.createElement("button")).update("-");
        Event.observe(btn, "click", this.delFilter.bindAsEventListener(this));
        row.insertCell(2).appendChild(btn);
    }, 

    /**
     * Delete a row of table or better, a filter! :D
     * @param {Event} e Generate event
     */
    delFilter: function(e) {
        this.table.deleteRow(Event.element(e).up("tr").rowIndex);
    }
}
