var _KjdaTranslation = (function($, _, Backbone, Handlebars, config) {
    "use strict";

    function App(appWrapSelector) {
        this.contentEl = $('[role=content]');
        this.sidebarEl = $('[role=sidebar]');
        this.currentView = null;
        this.languages = null;
        this.router = null;
        for (var p in this) {
            if (_.isFunction(this[p])) {
                _.bindAll(this, p);
            }
        }
    }
    App.prototype = {
        apiUrl: function(url){
          return this.config.baseUrl + url;  
        },
        start: function(cb) {
            var $this = this;
            this.languages = new Languages();
            this.reloadLanguages(function(){
                $this.renderSidebar();
                $this.setupRouting();
                !!cb && cb();
            });
        },
        reloadLanguages: function(cb){
            this.languages.fetch({
                success: function() {
                    !!cb && cb();
                },
                error: function(){
                  throw new Error("Can't load languages")
                }
            });
        },
        setupRouting: function() {
            var Router = Backbone.Router.extend({
                routes: this.routes()
            });
            this.router = new Router;
        },
        renderSidebar: function(){
            var view = new SidebarView({collection: this.languages});
            this.sidebarEl.append(view.render().$el);
        },
        renderView: function(view) {
            if (this.currentView) {
                this.currentView.remove();
            }
            this.currentView = view;
            this.currentView.render();
            this.contentEl.html('').append(this.currentView.$el);
        },
        indexView: function index() {
            var view = new AddLanguageView({collection: this.languages});
            this.renderView(view);
        },
        languageView: function language(id) {
            var view = new LanguageView({model: this.languages.get(id)});
            this.renderView(view);
        },
        routes: function(){
            return {
                "": this.indexView,
                "lang/:id": this.languageView
            }
        }
    }
    
    var Language = Backbone.Model.extend({
        url: function() {
            if (this.isNew()) {
                return config.baseUrl + '/api/languages'
            }
            return config.baseUrl + '/api/languages/' + this.id;
        }
    });

    var Languages = Backbone.Collection.extend({
        model: Language,
        url: config.baseUrl + '/api/languages',
        comparator: function(item) {
            return item.get('name').toLowerCase();
        }
    });

    var Translation = Backbone.Model.extend({
        url: function(){
            if( this.isNew() ){
                return config.baseUrl + '/api/translations/' + this.id
            }
            return config.baseUrl + '/api/translations/' + this.get('language').id + '/' + this.id
        }
    });
    var Translations = Backbone.Collection.extend({
        model: Translation,
        url: function() {
            return config.baseUrl + '/api/translations/' + this.languageId
        },
        initialize: function(args) {
            this.languageId = args.languageId;
        }
    });



    var SidebarView = Backbone.View.extend({
        ui: {},
        initialize: function() {
            this.template = Handlebars.compile($('#tmp-language-list').html());
            this.listenTo(this.collection, "reset", this.render);
            this.listenTo(this.collection, "add", this.render);
        },
        render: function() {
            this.$el.html(this.template({
                languages: this.collection.toJSON()
            }));
            this.ui.$contentEl = this.$('[role=content]');
            return this;
        },
        getContentElement: function(){
            return this.ui.$contentEl;
        }
    });
    
    var AddLanguageView = Backbone.View.extend({
        events: {
            "submit #addLanguage": "addLanguage"
        },
        ui: {
            
        },
        initialize: function(args) {
            this.collection = args.collection;
            this.template = Handlebars.compile($('#tmp-language-add').html());
            _.bindAll(this, "onNewLanguage", "onNewLanguageError")
            
        },
        render: function() {
            this.$el.html(this.template(  ));
            this.ui.$form = this.$('#addLanguage');
            this.ui.$status = this.$('[role="status"]');
            this.ui.$submitBtn = this.$('input[type=submit]');
            this.ui.$name = this.$('#name');
            this.ui.$locale = this.$('#locale');
            return this;
        },
        disableForm: function(){
            this.ui.$submitBtn.attr('disabled', true);
        },
        enableForm: function(){
            this.ui.$submitBtn.attr('disabled', false);
        },
        addLanguage: function(e) {
            e.preventDefault();
            this.disableForm();
            var language = new Language();
            this.clearStatus();
            language.save({
                name: this.ui.$name.val(),
                locale: this.ui.$locale.val()
            }, {
                success: this.onNewLanguage,
                error: this.onNewLanguageError
            });
        },
        onNewLanguage: function(language, response, options){
            this.collection.add(language);
            this.ui.$form.trigger("reset");
            this.enableForm();
            var msg = language.get('name') + ':' + language.get('locale') 
                        + ' added to your language list';
            this.status(msg, 'alert-info');
        },
        onNewLanguageError: function(model, xhr, options){
            this.enableForm();
            this.status(xhr.responseText, 'alert-danger');
        },
        status: function(msg, cls){
            this.ui.$status.addClass(cls).html(msg).fadeIn();
        },
        clearStatus: function(){
            this.ui.$status.removeClass('alert-danger alert-info').html('').hide();
        }
    });

    var LanguageView = Backbone.View.extend({
        model: Language,
        initialize: function() {
            this.template = Handlebars.compile($('#tmp-language').html());
            this.translations = new Translations({
                languageId: this.model.get('id')
            })
            //this.listenTo(this.model, "change", this.render);
            this.listenTo(this.translations, "reset", this.render);
            var $this = this;
            this.translations.fetch({
                success: function() {
                    $this.render();
                }
            });
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            var wrap = this.$('[role="translations"]');
            if ( this.translations.length ) {
                _.each(this.translations.models, function(model) {
                    var view = new TranslationView({model: model});
                    wrap.append(view.render().$el);
                }, this);
            }
            return this;
        }
    });

    var TranslationView = Backbone.View.extend({
        model: Translation,
        events: {
            'change [role="content"]' : 'saveTranslation',
            'click [role="saveBtn"]' : 'saveTranslation'
        },
        initialize: function() {
            this.template = Handlebars.compile($('#tmp-translation').html());
            //this.listenTo(this.model, "change", this.render)
            _.bindAll(this, "onSave");
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        },
        saveTranslation: function(){
            this.$('[role=status]').html('saving...');
            var content = this.$('[role="content"]').val();
            this.model.set('content', content);
            this.model.save(this.model.toJSON(), {
                success: this.onSave,
                error: this.onSaveError
            });
        },
        onSave: function(model, response, options){
            this.$('[role=status]').html('ok');
        },
        onSaveError: function(model, xhr, options){
            //var st = $('div').addClass('alert alert-error').html(xhr.responseText);
            //this.$('[role=status]').html(st);
        }
    });

    
    
    return App;
})($, _, Backbone, Handlebars, config);