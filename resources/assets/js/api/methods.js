'use strict';

export default (get, post, put, del) => {
    const
        save = (base, model, key='id') => model[key] ? put(base+'/'+model[key], model) : post(base, model),
        /* By default, api methods are neither cached nor queued */
        api = (func, options, default_) => {
            func.cached = options && options.cached || false;
            func.queued = options && options.queued || false;
            if (typeof default_ !== 'undefined') {
                func.default = default_;
            }
            return func
        },
        /* These methods are cached, so that the last successful call results are returned when internet is unavailable */
        cached = (func, default_) => api(func, { cached: true }, default_),
        /* These methods are queued and retried when the internet connection is restored */
        queued = (func, title, default_) => api(func, { queued: title }, default_)/*,
        cachedQueued = (func, title, default_) => api(func, { cached: true, queued: title }, default_)*/;

    /**
     * Each queued function can specify queue item title as a string or a callback,
     * and each default value can be a value or a factory. For either parameter in case of
     * function they will receive the same arguments the method did when it failed.
     */


    return {
        subscribe: api(data => post('/subscription', data)),

        validateReferrer: api(email => put('/admin/users/referrer/dry', { email })),

        updateReferrer: api(email => put('/admin/users/referrer', { email })),


        previewSubscription: cached(data => post('/subscription/preview', data)),

        unsubscribe: api(() => del('/subscription')),

        getDeathReasons: cached(() => get('/admin/users/getDeathReasons')),

        getUpcoming: cached(() => get('/admin/users/upcoming')),

        getTransferBreeders: cached(() => get('/admin/transfers/breeders')),

        getUsersPlans: cached(() => get('/admin/users/plans')),

        getBreedersList: cached(() => get('/admin/breeders/getList')),

        getLitters: cached(query => get('/admin/litters', query || {})),

        getLitterKitsWeighOfType: cached(
            (litter_id, type) => get('/admin/litters/' + litter_id + '/weigh', { animal_type: type })
        ),

        getKitsAutocomplete: cached(() => get('/admin/kits/autocomplete')),

        getDummyEvents: cached(date => get('/admin/events/breedPlanDummyEvents', { date })),

        getPlanEvents: cached(plan_id => get('/admin/plans/' + plan_id + '/events')),

        getEvent: cached(event_id => get('/admin/events/' + event_id)),

        getEvents: cached(query => get('/admin/events', query || {})),

        getPlans: cached(query => get('/admin/plans', query || {})),

        getPlansBreeders: cached(() => get('/admin/plans/breeders')),

        getLittersList: cached(() => get('/admin/litters/getList')),

        getTypeEvents: cached((type, id, query) => get('/admin/' + type + (id ? '/'+id : '') + '/events', query || {})),

        getBreeders: cached(query => get('/admin/breeders', query)),

        getBreederCategories: cached(() => get('/admin/breeders/categories')),

        getBreederCategoriesAutocomplete: cached(() => get('/admin/breeders/categories/autocomplete')),

        getUserSettingsData: cached(user_id => get('/admin/users/' + user_id + '/settings/getData')),

        getCurrentUserSettingsData: cached(() => get('/admin/users/settings/getDataCurrent')),

        // getReportsStatistics: cached(() => get('/admin/reports/statistics')),
        getReportsStatistics: cached(query => get('/admin/reports/statistics', query)),

        // getReportsDoes: cached(() => get('/admin/reports/does')),
        getReportsDoes: cached(query => get('/admin/reports/does', query)),

        // getReportsBucks: cached(() => get('/admin/reports/bucks')),
        getReportsBucks: cached(query => get('/admin/reports/bucks', query)),

        checkBreederId: cached(id => get('/admin/breeders/checkId', { id })),

        checkBreedersLimit: cached(
            archived => get('/admin/breeders/check' + (archived ? 'Archived' : '') + 'Limit'),
            { ok: true }
        ),

        getKit: cached(id => get('/admin/kits/' + id)),

        getKitPedigree: cached(id => get('/admin/kits/'+ id +'/getPedigree')),

        getLedgerStatistics: cached(query => get('/admin/ledger/statistics', query || {})),

        getLedgerCategories: cached(() => get('/admin/ledger/categories')),

        getLedgerAutocomplete: cached(() => get('/admin/ledger/autocomplete')),

        getLedgerEntries: cached(query => get('/admin/ledger/entries', query || {})),

        getLedgerCategoriesAutocomplete: cached(() => get('/admin/ledger/categories/autocomplete')),

        getLitterKits: cached(litter_id => get('admin/litters/' + litter_id + '/getKits')),

        getLitterDied: cached(litter_id => get('admin/litters/' + litter_id + '/getDied')),

        getLitter: cached(id => get('/admin/litters/' + id)),

        getPedigree: cached(id => get('/admin/pedigrees/' + id)),

        getBreederPedigree: cached(breeder_id => get('/admin/breeders/' + breeder_id + '/getPedigree')),

        getBreeder: cached(id => get('/admin/breeders/' + id)),

        getBreederLitters: cached(breeder_id => get('/admin/breeders/'+ breeder_id + '/getLitters')),

        getRolesList: cached(() => get('/admin/roles/getList')),

        getUser: cached(id => get('/admin/users/' + id)),

        getUsers: cached(query => get('/admin/users', query || {}).then(data => data.users)),

        getSubscriptionInvoices: cached(() => get('subscription/invoices').then(data => data.invoices)),

        getSubscriptionPlans: cached(() => get('subscription/plans').then(data => data.plans)),

        getSubscription: cached(() => get('subscription')),

        getUsersDashboard: cached(() => get('/admin/users/dashboard')),

        getUserReferrals: cached(() => get('/admin/users/referrals')),

        saveLedgerEntry: queued(entry => save('/admin/ledger/entries', entry),
            entry => (entry.id?'Edit':'Add')+' ledger item "'+entry.name+'" ('+(entry.debit?'+':'-')+'$'+entry.amount+')',
            entry => entry),

        deleteLedgerEntry: queued(entry => del('/admin/ledger/entries/'+entry.id),
            entry => 'Delete ledger item "'+entry.name+'"',
            []),

        archiveLedgerEntry: queued(entry => post('/admin/ledger/entries/'+entry.id+'/archive', { archived: 1 }),
            entry => 'Archive ledger item "'+entry.name+'"',
            []),

        unarchiveLedgerEntry: queued(entry => post('/admin/ledger/entries'+entry.id+'/unarchive', { archived: 0}),
            entry => 'Unarchive ledger item "'+entry.name+'"',
            []),


        saveLedgerCategory: queued(category => save('/admin/ledger/categories', category),
            category => (category.id?'Edit':'Add')+' ledger category "'+category.name+'"',
            category => category),

        deleteLedgerCategory: queued(category => del('/admin/ledger/categories/'+category.id),
            category => 'Delete ledger category "'+category.name,
            []),


        saveBreederCategory: queued(category => save('/admin/breeders/categories', category),
            category => (category.id?'Edit':'Add')+' breeders category "'+category.name+'"',
            category => category),

        deleteBreederCategory: queued(category => del('/admin/breeders/categories/'+category.id),
            category => 'Delete ledger category "'+category.name,
            []),


        missBirth: queued(birth => post('/admin/plans/'+birth.breedplan+'/missed'),
            'Miss a birth', []),

        recordBirth: queued(birth => post('/admin/litters', birth),
            'Record a birth', []),


        saveKit: queued(kit => save('/admin/kits', kit),
            kit => (kit.id?'Edit':'Add')+' rabbit kit #'+kit.given_id,
            kit => kit),

        deleteKit: queued(kit => del('/admin/kits/'+kit.id),
            kit => 'Delete rabbit kit #'+kit.given_id,
            []),

        archiveKit: queued(kit => post('/admin/kits/'+kit.id+'/archive', { archived: 1}),
            kit => 'Archive rabbit kit #'+kit.given_id,
            []),

        unarchiveKit: queued(kit => post('/admin/kits/'+kit.id+'/archive', { archived: 0 }),
            kit => 'Unarchive kit #' + kit.given_id, []),

        sellKit: queued((kit, value) => post('/admin/kits/'+kit.id+'/sold', { sold: 1, value }),
            (kit, value) => 'Sell kit #'+kit.given_id+' for $'+value,
            []),

        unsellKit: queued(kit => post('/admin/kits/'+kit.id+'/sold', { sold: 0 }),
            kit => 'Undo sell kit #'+kit.given_id,
            []),

        transferKit: queued((kit, email) => post('/admin/kits/'+kit.id+'/transfer', { email }),
            (kit, email) => 'Transfer kit #'+kit.given_id+' to &lt;'+email+'&gt;',
            []),

        dieKit: queued((kit, query) => get('/admin/kits/'+kit.id+'/died', query),
            kit => 'Mark kit #'+kit.given_id+' as dead',
            []),

        reviveKit: queued(kit => post('/admin/kits/'+kit.id+'/revived'),
            kit => 'Remove died mark from kit #'+kit.given_id,
            []),

        makeBreeder: queued(kit => get('/admin/kits/'+kit.id+'/makeBreeder'),
            kit => 'Create a breeder from kit #'+kit.given_id,
            []),


        saveBreeder: queued(breeder => save('/admin/breeders', breeder),
            breeder => (breeder.id?'Edit':'Add')+' breeder "'+breeder.name+'"',
            breeder => breeder),

        saveBreeders: queued((breeders, fields, emptyBreeder) => post('/admin/breeders/bulk', {breeders, fields, emptyBreeder}),
            'Save breeders', []),
            
        saveLedgers: queued((ledgers, emptyLedger) => post('/admin/ledgers/bulk', {ledgers, emptyLedger}),
        	'Save ledgers', []),
        
        save: queued((breeders, fields, emptyBreeder) => post('/admin/breeders/bulk', {breeders, fields, emptyBreeder}),
                    'Save breeders', []),
        archiveBreeder: queued(breeder => post('/admin/breeders/'+breeder.id+'/archive', { archived: 1 }),
            breeder => 'Archive breeder "'+breeder.name+'"',
            []),

        unarchiveBreeder: queued(breeder => post('/admin/breeders/'+breeder.id+'/archive', { archived: 0 }),
            breeder => 'Unarchive rabbit breeder "'+breeder.name+'"',
            []),

        sellBreeder: queued((breeder, value) => post('/admin/breeders/'+breeder.id+'/sold', { sold: 1, value }),
            (breeder, value) => 'Sell breeder "'+breeder.name+'" for $'+value,
            []),

        unsellBreeder: queued(breeder => post('/admin/breeders/'+breeder.id+'/sold', { sold: 0 }),
            breeder => 'Undo sell breeder "'+breeder.name+'"',
            []),

        transferBreeder: queued((breeder, email) => post('/admin/breeders/'+breeder.id+'/transfer', { email }),
            (breeder, email) => 'Transfer breeder "'+breeder.name+'" to &lt;'+email+'&gt;',
            []),

        deleteBreeder: queued(breeder => del('/admin/breeders/'+breeder.id),
            breeder => 'Delete breeder "'+breeder.name+'"',
            []),


        savePedigree: queued(pedigree => save('/admin/pedigrees', pedigree),
            pedigree => (pedigree.id?'Edit':'Add')+' pedigree record "'+pedigree.name+'"',
            []),


        savePlan: queued(plan => save(plan.id?'/admin/plans':'/admin/events/makeBreedPlan', plan),
            plan => (plan.id?'Edit':'Add')+' breeding plan',
            plan => plan),

        deletePlan: queued(plan => del('/admin/plans/'+plan.id),
            'Delete a breeding plan', []),


        saveEvent: queued(event => save('/admin/events', event),
            event => (event.id?'Edit':'Add')+' task',
            event => event),

        closeEvent: queued(event => get('/admin/events/'+event.id+'/close'),
            'Close task', []),

        reopenEvent: queued(event => get('/admin/events/'+event.id+'/reopen'),
            'Reopen task', []),

        archiveEvent: queued(event => post('/admin/events/'+event.id+'/archive'),
            'Archive task', []),

        unarchiveEvent: queued(event => post('/admin/events/'+event.id+'/unarchive'),
            'Unarchive task', []),

        deleteEvent: queued(event => del('/admin/events/'+event.id),
            'Delete task', []),

        archiveEvents: queued(events => post('/admin/events/archiveEvents', { events }),
            events => 'Archive group of '+events.length+' tasks'
            , []),


        saveLitter: queued(litter => save('/admin/litters', litter),
            litter => (litter.id?'Edit':'Add')+' litter #'+litter.id,
            []),

        archiveLitter: queued(litter => post('/admin/litters/'+litter.id+'/archive', { archived: 1 }),
            litter => 'Archive litter #'+litter.id,
            []),

        unarchiveLitter: queued(litter => post('/admin/litters/'+litter.id+'/archive', { archived: 0 }),
            litter => 'Unarchive litter #'+litter.id,
            []),

        deleteLitter: queued(litter => del('/admin/litters/'+litter.id),
            litter => 'Delete litter #'+litter.id,
            []),


        acceptTransfer: queued(transfer => get('/admin/transfer/' + (transfer.transfer_id || transfer.id)),
            transfer => 'Accept transfer of breeder '+(transfer.name?'"'+transfer.name+'"':'#'+transfer.tattoo),
            []),

        declineTransfer: queued(transfer => del('/admin/transfer/' + (transfer.transfer_id || transfer.id)),
            transfer => 'Decline transfer of breeder '+(transfer.name?'"'+transfer.name+'"':'#'+transfer.tattoo),
            []),


        saveUser: queued(user => save('/admin/users', user),
            user => (user.id?'Edit':'Add')+' user "'+user.name+'"',
            user => user),

        deleteUser: queued(user => del('/admin/users/'+user.id),
            user => 'Delete user "'+user.name+'"',
            []),


        postLitterButcherValue: queued(
            (litter_id, value) => post('/admin/litters/'+litter_id+'/butcherValue', { value }),
            (litter_id, value) => 'Butcher litter #'+litter_id+' ($'+value+')',
            []),

        postBreederButcherValue: queued(
            (breeder_id, value) => post('/admin/breeders/'+breeder_id+'/butcherValue', { value }),
            (breeder_id, value) => 'Butcher breeder #'+breeder_id+' ($'+value+')',
            []),

        postBreederDeathReasonValue: queued(
            (breeder_id, value) => post('/admin/breeders/'+breeder_id+'/deathReasonValue', { value }),
            (breeder_id, value) => 'Death reason breeder #'+breeder_id+' ($'+value+')',
            []),

        postKitDeathReasonValue: queued(
            (kit_id, value) => post('/admin/kits/'+kit_id+'/deathReasonValue', { value }),
            (kit_id, value) => 'Death reason kit #'+kit_id+' ($'+value+')',
            []),

        postUserSettings: queued((user_id, settings) => post('/admin/users/'+user_id+'/settings', settings),
            () => 'Update user settings',
            []),

        postLitterWeigh: queued((litter, data) => post('/admin/litters/'+litter.id+'/weigh', data),
            (litter, data) => 'Send litter weight',
            []),

        butcherKits: queued(data => post('/admin/kits/butch', data),
            data => 'Butcher litter #'+data.litter_id,
            []),

        butcherBreeder: queued((breeder, data) => post('/admin/breeders/'+breeder.id+'/butch', data),
            data => 'Butcher Breeder #'+data.breeder_id,
            []),

        diedBreeder: queued((breeder, data) => post('/admin/breeders/'+breeder.id+'/died', data),
            data => 'Died Breeder #'+data.breeder_id,
            []),

        addReferral: queued(email => post('/admin/users/referrals', { email }),
            email => 'Add &lt;'+email+'&gt; as your referral',
            []),

        getOptionsForCopyPedigree: cached(
            (type, id, params = {}) => get('/admin/pedigrees/copy/options', Object.assign({ type, exclude: id }, params)).then(
                data => data.options
            )
        ),

        copyPedigree: queued((type, from, to, line) => post('/admin/pedigrees/copy', { type, from, to, line }),
            (type, from, to) => 'Copy pedigree',
            []),


        getPlanNextLitterId: queued((plan_id) => get('/admin/plans/nextLitterId/' + plan_id)),

        getUserSocialAccounts: cached(() => get('/admin/user/socials').then(data => data.social_accounts)),

        disconnectSocial: queued(social_id => del('/admin/user/socials/' + social_id), () => 'Disconnect social account'),

        importFile: queued(file => {
            const data = new FormData();
            data.append('import', file);
            return post('/admin/import', data)
        }, () => 'Upload file for import'),
        
        importLedgersFile: queued(file => {
            const data = new FormData();
            data.append('import', file);
            return post('/admin/import/ledgers', data)
        }, () => 'Upload file for import'),

        saveWebPushEndpoint: queued(
            (endpoint, resetCreds, publicKey, authToken) => post('/admin/user/web-push-endpoint', {
                endpoint, 'reset-creds': resetCreds, publicKey, authToken
            }),
            () => 'Subscribe to notifications'
        ),

        requestTestNotification: queued(() => post('/admin/user/request-test-notification'),
                                        () => 'Request test notification'),

        /**
         * Cage card templates routes.
         */
        // Get list of all templates.
        getCageCardsTemplates: cached(query => get('/admin/cage-cards/templates', query)),

        getCageCardTemplateFieldsOptionsList: cached(query => get('/admin/cage-cards/fields-list')),

        // Delete template.
        deleteCageCardTemplate: queued(
            template => del('/admin/cage-cards/templates/' + template.id),
            template => 'Delete template "' + template.name + '"',
            []
        ),

        // Delete template.
        copyCageCardTemplate: queued(
            template => post('/admin/cage-cards/templates/' + template.id + '/copy'),
            template => 'Copy template "' + template.name + '"',
            []
        ),

        // Store cage card template.
        storeCageCardTemplate: queued(template => save('/admin/cage-cards/templates', template),
            template => 'Add template "' + template.name + '"',
            template => template),

        // Update cage card template.
        updateCageCardTemplate: queued(template => save('/admin/cage-cards/templates', template),
            template => 'Edit template "' + template.name + '"',
            template => template),

        // Get first entities for preview (breeder and litter).
        getFirstEntitiesForPreview: cached(query => get('/admin/cage-cards/first-entities', query)),

        // Get list of user's breeders.
        getCageCardBreedersList: cached(query => get('/admin/cage-cards/breeders', query)),

        // Get list of user's litters.
        getCageCardLittersList: cached(query => get('/admin/cage-cards/litters', query)),

        // Print batch.
        printBatch: cached(query => post('/admin/cage-cards/print-batch', query)),
    };
};
