<template>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-12">
        <ExchangeRatesComponent />

        <ReportsComponent />
    </div>
</template>

<script>
import ExchangeRatesComponent from '@/components/exchangeRates'
import ReportsComponent from '@/components/reportsPanel'
export default {
    name: 'Dashboard',
    components: {
        ExchangeRatesComponent,
        ReportsComponent,
    },
}
</script>
