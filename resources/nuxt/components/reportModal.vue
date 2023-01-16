<template>
    <div v-if="report" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="pointer-events-none fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="pointer-events-auto fixed inset-40 z-10">
            <div class="absolute -top-2 -right-2 z-40 flex">
                <button @click="report = null" type="button"
                        class="bg-gray-300 text-gray-500 h-6 w-6 text-center rounded-full shadow-sm hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                    <span class="sr-only">Close panel</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div
                class="flex h-full w-full items-end justify-center p-4 text-center overflow-hidden">
                <div
                    class="relative transform overflow-auto h-full w-full rounded-lg bg-white pb-4 text-left shadow-xl transition-all">

                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ report.currencies }} : {{ report.from }} -> {{ report.to }}
                        </h3>

                        <span v-if="isPagedReport" class="isolate inline-flex rounded-md shadow-sm">
                          <button @click="decrement" type="button"
                                   class="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                          </button>
                          <button @click="increment" type="button"
                                   class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                          </button>
                        </span>
                    </div>


                    <table class="fixed w-full divide-y divide-gray-300">
                        <thead class="sticky top-0 bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                Date
                            </th>
                            <th v-for="(currency, index) in report.currencies" scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                {{ currency }}
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(currency, index) in reportRateData">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ index }}
                                </td>
                                <td v-for="(exchangeRate, index) in currency"
                                    class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ exchangeRate.rate }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        report: {
            type: Object|null,
            default: null,
            required: true,
        }
    },
    data() {
        return {
            currentIndex: 0,
        }
    },
    computed: {
        reportRateData() {
            if (!this.isPagedReport) {
                return this.report.rates
            }

            return this.report.rates[Object.keys(this.report.rates)[this.currentIndex]]
        },
        isPagedReport() {
            return ['weekly', 'monthly'].includes(this.report.interval)
        }
    },
    methods: {
        increment() {
            if (this.currentIndex === null || this.currentIndex === 0) {
                return
            }

            this.currentIndex++
        },
        decrement() {
            if (this.currentIndex === this.report.rates.length) {
                return
            }

            this.currentIndex--
        },
    },
}
</script>
