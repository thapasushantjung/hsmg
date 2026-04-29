import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

import { bedGrid } from '@/routes';

interface Tenant {
    id: number;
    name: string;
}

interface Booking {
    id: number;
    status: string;
    tenant: Tenant;
}

interface BedData {
    id: number;
    name: string;
    status: 'available' | 'occupied' | 'maintenance';
    monthly_rate: number;
    current_assignment?: {
        booking: {
            status: string;
            tenant: Tenant;
        }
    };
}

interface Room {
    id: number;
    name: string;
    capacity: number;
    beds: BedData[];
}

interface Floor {
    id: number;
    name: string;
    level: number;
    rooms: Room[];
}

export default function BedGrid({ floors }: { floors: Floor[] }) {
    const getBedColor = (status: string) => {
        switch (status) {
            case 'available': return 'bg-emerald-100 border-emerald-500 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-400';
            case 'occupied': return 'bg-rose-100 border-rose-500 text-rose-800 dark:bg-rose-900/30 dark:border-rose-500 dark:text-rose-400';
            case 'maintenance': return 'bg-amber-100 border-amber-500 text-amber-800 dark:bg-amber-900/30 dark:border-amber-500 dark:text-amber-400';
            default: return 'bg-gray-100 border-gray-500 text-gray-800';
        }
    };

    return (
        <>
            <Head title="Bed Grid" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Bed Grid</h1>
                        <p className="text-sm text-muted-foreground">Manage your physical assets and current assignments.</p>
                    </div>
                    <div className="flex items-center gap-3 text-sm">
                        <div className="flex items-center gap-1.5"><div className="h-3 w-3 rounded-full bg-emerald-500"></div> Available</div>
                        <div className="flex items-center gap-1.5"><div className="h-3 w-3 rounded-full bg-rose-500"></div> Occupied</div>
                        <div className="flex items-center gap-1.5"><div className="h-3 w-3 rounded-full bg-amber-500"></div> Maintenance</div>
                    </div>
                </div>

                <div className="flex flex-col gap-8">
                    {floors.map((floor) => (
                        <div key={floor.id} className="flex flex-col gap-4">
                            <h2 className="text-xl font-bold tracking-tight border-b pb-2">{floor.name}</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                {floor.rooms.map((room) => (
                                    <Card key={room.id} className="flex flex-col">
                                        <CardHeader className="pb-3 border-b">
                                            <CardTitle className="text-base font-semibold">
                                                {room.name}
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent className="p-4 grid grid-cols-2 gap-3">
                                            {room.beds.map((bed) => {
                                                const activeBooking = bed.current_assignment?.booking;
                                                const isOccupied = bed.status === 'occupied';

                                                return (
                                                    <div 
                                                        key={bed.id} 
                                                        className={`flex items-center justify-center h-20 p-3 rounded-lg border-2 cursor-pointer transition-all hover:shadow-md ${getBedColor(bed.status)}`}
                                                    >
                                                        <span className="font-bold">Bed {bed.name}</span>
                                                    </div>
                                                );
                                            })}
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    ))}
                    
                    {floors.length === 0 && (
                        <div className="text-center py-12 border-2 border-dashed rounded-lg text-muted-foreground">
                            No floors configured yet.
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

BedGrid.layout = {
    breadcrumbs: [
        {
            title: 'Bed Grid',
            href: bedGrid(),
        },
    ],
};
