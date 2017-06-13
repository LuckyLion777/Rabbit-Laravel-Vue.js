const emptyLedger = App.emptyLedger;
emptyLedger.aquired = null;

const WizardLedger = {
    template: "#wizard__ledger-template",
    data: function () {
        return {
        	categories: [],
        	breeders: [],
        	litters: []
        }
    },
    props: ['ledger', 'user', 'autocomplete'],
    components: {
        
    },
    methods: {
    	
    	loadCategories: function () {
            api.getLedgerCategories().then(data => {
                this.categories = data.categories;
            });
        },
        checkIfAssociated: function (ledger)
        {
        	for(var i = 0; i < this.categories.length; i++)
        	{
        		var category = this.categories[i];
        		if(category.id == ledger.category_id)
        		{
        			if(category.special == 'breeder' || category.special == 'litter')
        			{
        				return true;
        			}
        		}
        	}
        	return false;
        },
        getAssociationType: function(ledger)
        {
        	var currentCategory = null;
        	if(typeof ledger.category_id !== 'undefined' && ledger.category_id != '')
        	{
        		for(var i = 0; i < this.categories.length; i++)
        		{
        			var category = this.categories[i];
        			if(category.id == ledger.category_id)
        			{
        				currentCategory = category;
        				break;
        			}
        		}
        		if(currentCategory.special == 'breeder' || currentCategory.special == 'litter')
            	{
            		if(currentCategory.special == 'litter')
            		{
            			return 'litter';
            		}
            		if(currentCategory.special == 'breeder')
            		{
            			return 'breeder';
            		}
            	}
        	}
        	return 'none';
        },
        loadLitters: function () {
            api.getLittersList().then(litters => {
                this.litters = litters.map(function(item) {
                    item.name = (item.parents[0] || {name: ''}).name + '/' + (item.parents[1] || {name: ''}).name;
                    return item;
                });
            });
        },
        loadBreeders: function () {
            api.getBreedersList().then(data => {
                this.breeders = data.bucks.concat(data.does);
            });
        },
        representAssociation: function(ledger, association) {
        	var currentCategory = null;
        	for(var i = 0; i < this.categories.length; i++)
        	{
        		var category = this.categories[i];
        		if(category.id == ledger.category_id)
        		{
        			currentCategory = category;
        			break;
        		}
        	}
            return currentCategory.special === 'breeder'
                    ? association.name + ': ' + association.tattoo
                    : association.given_id + ': ' + association.name;
        },
    },
    ready: function () {
        this.loadCategories();
        this.loadBreeders();
        this.loadLitters();
    }
};

const WizardLedgerForm = {
    template: "#wizard__ledger-form-template",
    data: function () {
        return {
            initialized: false
        }
    },
    props: ['ledger', 'user', 'autocomplete', 'current'],
    components: {
        
    },

    methods: {
        
    },

    ready: function () {
        
    }
};

App.Components.WizardLedgers = {
    template: "#wizard__ledgers-template",
    data: function () {
        return {
            user: {},
            ledgersNumber: 0,
            mode: null,
            ledgers: [],
            current: {},
            loading: false,
            autocomplete: {}
        }
    },
    props: [],
    components: {
        'ledger' : WizardLedger,
        'ledger-form': WizardLedgerForm,

    },
    computed: {
    	
    },
    watch:{
        ledgersNumber(value, oldValue){
            if(value < oldValue){
                this.ledgers = this.ledgers.slice(0, value - 1);
            }

            if(value > 50){
                value = 50;
                this.ledgersNumber = 50;
            } else {
                this.setLedgersRows(value);
            }
        }
    },
    methods: {
    		
        addRow(){
            // let ledger = Object.assign({}, emptyLedger);
            // this.ledgers.push(ledger);
            this.ledgersNumber +=1;
        },

        setLedgersRows(value){
            while(this.ledgers.length < value){
                let ledger = Object.assign({}, emptyLedger);
                this.ledgers.push(ledger);
            }
        },
        saveLedgers(){
            if(!this.loading){

                this.loading = true;

                api.saveLedgers(this.ledgers, emptyLedger).then((response) => {
                    this.loading = false;
                    this.$router.go({ path: '/ledger' });
                }, response => {
                    console.log('error', response)
                    this.loading = false;
                });
            }
        },
        nextLedger(){
            if(this.current < this.ledgersNumber - 1){
                this.current +=1;
            } else {
                $('#wizard-ledger-modal').modal('hide');
            }
        },
        previousLedger(){
            if(this.current > 0){
                this.current--;
            }
        },

        parseFile(e) {
            if (!$(e.target).val()) {
                return;
            }
            api.importLedgersFile(e.target.files[0]).then(
                data => {
                    for (const ledger of data.ledgers) {
                    	ledger.aquired = ledger.acquired;
                        delete ledger.acquired;

                        for (const key in ledger) {
                            if (ledger.hasOwnProperty(key) && !ledger[key])  {
                                delete ledger[key];
                            }
                        }

                        this.ledgers.push(Object.assign({}, emptyLedger, ledger));
                        this.ledgersNumber += 1;
                    }
                    this.mode = 'import';
                },
                errors => {
                    this.importErrors = errors.data;
                }
            );
        },
        beginManual() {
            this.ledgersNumber = 2;
            this.mode = 'manual';
        },
    },

    ready: function () {
    	this.parseFile(this.$router.ledgersFileInput);
    },
    route: {
        activate: function () {
            window.scrollTo(0, 0);
        },
    }
};