<template>
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="bg-white px-6 py-5 my-4">
            <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">
                <div class="ml-4 mt-2">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Live Conversion</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-400">Select up to 5 currencies</p>
                </div>
                <div class="w-1/2" v-if="currencies">
                    <label class="sr-only">Select currencies to compare</label>
                    <multiselect
                        class="text-gray-700"
                        v-model="toConvert"
                        :options="currencies"
                        :multiple="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        placeholder="Select currencies to compare"
                        label="code"
                        track-by="name"
                        :max-height="255"
                        :max="5"
                        @close="fetchCurrencyConversions"
                        :searchable="!submitting"
                    >
                        <template slot="option" slot-scope={option}>
                            <strong>{{ option.code }}</strong>
                            <small class="text-sm ml-3">{{ option.name }}</small>
                        </template>
                    </multiselect>
                </div>
            </div>
        </div>
        <div class="border-t h-64 border-gray-200">
            <table v-if="conversions" class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Currency
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Exchange Rate
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(exchangeRate, index) in conversions">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ exchangeRate.currency }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ exchangeRate.rate }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="flex h-full flex-1 items-center justify-center text-gray-400 font-light">
                Please select currencies to view exchange rate data
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters} from 'vuex'
import Multiselect from 'vue-multiselect'

export default {
    name: 'ExchangeRateComponent',
    components: {
        Multiselect,
    },
    computed: {
        ...mapGetters({
            currencies: 'currency/currencies'
        })
    },
    data() {
        return {
            toConvert: [],
            conversions: null,
            submitting: false,
        }
    },
    methods: {
        fetchCurrencyConversions() {
            if (this.toConvert.length === 0) {
                return
            }

            this.submitting = true;

            this.$axios.post('exchange-rates', {
                'currencies': this.toConvert.map((currency) => currency.code)
            }).then((response) => {
                this.conversions = response.data
            }).finally(() => {
                this.submitting = false
            })
        }
    },
    mounted() {
        if (this.currencies) {
            return
        }

        this.$axios.get('currencies').then(response => {
            this.$store.dispatch('currency/store', response.data)
        })
    }
}
</script>
