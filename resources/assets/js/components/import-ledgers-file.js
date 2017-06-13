Vue.component('import-ledgers-file', {
	template: '<a href="#" v-on:click="triggerFileInput($event)">Import</a><input @change="parseLedgerFile($event)" type="file" v-el:ledgersfileinput id="import-ledgers-file" style="display: none">',
	methods: {
		triggerFileInput: function(e)
		{
			e.preventDefault();
			this.$els.ledgersfileinput.click();
			e.stopPropagation();
		},
		parseLedgerFile: function(e)
		{
			this.$router.ledgersFileInput = e;
			this.$route.router.go('/wizard/ledgers');
		}
	}
});