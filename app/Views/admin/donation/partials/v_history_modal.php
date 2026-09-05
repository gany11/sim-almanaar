<template x-teleport="body">

    <div
        x-data="historyModal()"
        x-show="open"
        x-cloak
        @open-history-modal.window="
            nama = $event.detail.namaDonatur || '-';
            kwitansi = $event.detail.kwitansi || '-';
            items = Array.isArray($event.detail.histories)
                ? $event.detail.histories
                : [];
            open = true;
        "
        @keydown.escape.window="open = false"
        class="fixed inset-0 flex items-center justify-center p-4
               bg-black/50 backdrop-blur-sm"

        style="z-index: 2147483647;"
    >

        <div
            @click.outside="open = false"
            class="relative bg-white rounded-3xl shadow-2xl border border-gray-100
                   w-full max-w-2xl max-h-[90vh]
                   overflow-hidden flex flex-col"
            x-transition
        >

            <!-- HEADER -->
            <div class="px-6 py-4 border-b border-gray-100
                        flex justify-between items-center
                        bg-gray-50/50 shrink-0">

                <div>
                    <h3 class="font-bold text-gray-800 text-base">
                        Riwayat Status Donasi
                    </h3>

                    <p class="text-xs text-gray-500 mt-0.5">
                        Donatur:
                        <span
                            class="font-semibold text-gray-700"
                            x-text="nama"
                        ></span>

                        <span class="mx-1">•</span>

                        <span
                            class="font-mono text-gray-600"
                            x-text="kwitansi"
                        ></span>
                    </p>
                </div>

                <button
                    @click="open = false"
                    type="button"
                    class="p-2 text-gray-400 hover:text-gray-600
                           rounded-full hover:bg-gray-100 transition-colors"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

            </div>


            <!-- BODY -->
            <div class="overflow-y-auto min-h-0">

                <template x-if="items.length === 0">

                    <div class="text-center py-12
                                text-gray-400 italic text-sm">

                        Belum ada riwayat status tercatat.

                    </div>

                </template>


                <template x-if="items.length > 0">

                    <div class="overflow-x-auto pb-6">

                        <div class="min-w-max px-8 pt-8">

                            <div class="relative">

                                <!-- GARIS -->
                                <div
                                    class="absolute top-4 left-4 right-4
                                           h-0.5 bg-purple-200"
                                ></div>


                                <!-- TIMELINE -->
                                <div
                                    class="relative flex justify-between gap-10"
                                >

                                    <template
                                        x-for="(h, index) in orderedItems"
                                        :key="
                                            h.id_histori_status_donasi || index
                                        "
                                    >

                                        <div
                                            class="relative w-44
                                                   flex-shrink-0 text-center"
                                        >

                                            <!-- BULLET -->
                                            <div
                                                class="relative z-10 mx-auto
                                                       w-8 h-8 rounded-full
                                                       bg-purple-500 text-white
                                                       flex items-center
                                                       justify-center
                                                       text-xs font-bold
                                                       shadow-sm
                                                       border-2 border-white"
                                                x-text="index + 1"
                                            ></div>


                                            <!-- STATUS -->
                                            <div class="mt-4">

                                                <span
                                                    class="inline-block
                                                           max-w-full
                                                           px-2.5 py-1
                                                           rounded-lg
                                                           text-[10px]
                                                           font-bold
                                                           uppercase
                                                           leading-tight"
                                                    :class="
                                                        h.class_color ||
                                                        'bg-gray-100 text-gray-700'
                                                    "
                                                    x-text="
                                                        h.status_donasi ||
                                                        'Tidak Terdefinisi'
                                                    "
                                                ></span>

                                            </div>


                                            <!-- TANGGAL -->
                                            <div class="mt-3">

                                                <p
                                                    class="text-[11px]
                                                           text-gray-400
                                                           font-mono
                                                           leading-relaxed"
                                                    x-text="
                                                        h.waktu_formatted ||
                                                        h.waktu ||
                                                        '-'
                                                    "
                                                ></p>

                                            </div>


                                            <!-- PENGURUS -->
                                            <div class="mt-2">

                                                <p
                                                    class="text-[10px]
                                                           text-gray-400
                                                           leading-relaxed"
                                                >
                                                    Dicatat oleh
                                                </p>

                                                <p
                                                    class="text-[11px]
                                                           font-semibold
                                                           text-gray-700
                                                           leading-relaxed
                                                           mt-0.5"
                                                    x-text="
                                                        h.nama_pengurus || '-'
                                                    "
                                                ></p>

                                            </div>

                                        </div>

                                    </template>

                                </div>

                            </div>

                        </div>

                    </div>

                </template>

            </div>


            <!-- FOOTER -->
            <div
                class="px-6 py-3 border-t border-gray-100
                       bg-gray-50/50 flex justify-end shrink-0"
            >

                <button
                    @click="open = false"
                    type="button"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300
                           text-gray-700 font-semibold rounded-xl
                           text-xs transition-colors"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>

</template>