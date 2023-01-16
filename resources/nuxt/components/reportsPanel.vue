<template>
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="bg-white px-6 py-5 my-4">
            <div class="space-y-4">
                <div class="my-2">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Request Exchange Rate Report</h3>
                </div>
                <form @submit.prevent="requestNewReport" v-if="currencies"
                      class="flex flex-row items-end justify-between">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700">Select currencies to compare</label>
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
                            :searchable="!submitting"
                        >
                            <template slot="option" slot-scope={option}>
                                <strong>{{ option.code }}</strong>
                                <small class="text-sm ml-3">{{ option.name }}</small>
                            </template>
                        </multiselect>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Report Range</label>
                        <select v-model="range" id="location" name="location"
                                 class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option v-for="range in ranges" :value="range">{{ range }}</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Request
                    </button>
                </form>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <table v-if="reports" class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Currencies
                        </th>
                        <th scope="col"
                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Format
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            From
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            To
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(report, index) in reports" class="cursor-pointer hover:bg-gray-100"
                        @click="displayReport(report)">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ report.currencies.join(', ') }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ report.interval }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ report.from }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ report.to }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ report.status }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="flex py-12 h-full flex-1 items-center justify-center text-gray-400 font-light">
                No reports to display... create one?
            </div>
        </div>

        <ReportModal v-if="report" :report="report" />

    </div>
</template>

<script>
import { mapGetters} from 'vuex'
import Multiselect from 'vue-multiselect'
import ReportModal from '@/components/reportModal'

export default {
    name: 'ExchangeRateReportsComponent',
    components: {
        Multiselect,
        ReportModal,
    },
    computed: {
        ...mapGetters({
            currencies: 'currency/currencies'
        })
    },
    data() {
        return {
            toConvert: [],
            reports: null,
            report: null,
            submitting: false,
            range: 'Last 12 Months',
            ranges: [
                'Last 12 Months',
                'Last 6 Months',
                'Last Month',
            ],
        }
    },
    methods: {
        displayReport(report) {
            if (this.submitting) {
                return
            }

            this.submitting = true

            this.$axios.get(`reports/${report.id}`).then((response) => {
                this.report = response.data
            }).finally(() => {
                this.submitting = false
            })
        },
        requestNewReport() {
            if (this.submitting || this.toConvert.length === 0) {
                return
            }

            this.submitting = true

            this.$axios.post(`reports`, {
                'currencies': this.toConvert.map((currency) => currency.code),
                'range': this.range
            }).then((response) => {
                this.reports.push(response.data)
            }).finally(() => {
                this.submitting = false
                this.toConvert = []
            })
        }
    },
    mounted() {
        this.$axios.get('reports').then((response) => {
            this.reports = response.data
        })

        // poll for updated reports would go here
    }
}
</script>
