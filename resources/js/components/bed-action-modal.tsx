import { useState, useEffect } from 'react';
import { useForm, router, Link } from '@inertiajs/react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Loader2, UserPlus, LogOut, ArrowRightLeft, User, Search } from 'lucide-react';
import { toast } from 'sonner';
import { tenants } from '@/routes'; // Assuming we have a route helper for tenants

// Interfaces matching BedGrid
interface TenantData {
    id: number;
    name: string;
    full_name?: string;
    phone?: string;
}

interface BookingData {
    status: string;
    tenant: TenantData;
    check_in_date?: string;
}

interface BedData {
    id: number;
    name: string;
    status: 'available' | 'occupied' | 'maintenance';
    monthly_rate: number;
    current_assignment?: {
        booking: BookingData;
    };
    room_name?: string;
}

interface BedActionModalProps {
    isOpen: boolean;
    onClose: () => void;
    bed: BedData | null;
}

export function BedActionModal({ isOpen, onClose, bed }: BedActionModalProps) {
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState<TenantData[]>([]);
    const [selectedTenantId, setSelectedTenantId] = useState<number | null>(null);
    const [isSearching, setIsSearching] = useState(false);
    const [availableBeds, setAvailableBeds] = useState<{id: number, name: string, room_name: string}[]>([]);
    const [selectedTransferBedId, setSelectedTransferBedId] = useState<string>('');
    const [rentAmount, setRentAmount] = useState<string>('');

    // Fetch available beds when modal opens for an occupied bed
    useEffect(() => {
        if (isOpen && bed?.status === 'occupied') {
            fetch('/api/beds/available')
                .then(res => res.json())
                .then(data => setAvailableBeds(data))
                .catch(console.error);
        }
    }, [isOpen, bed]);

    // Handle tenant search
    useEffect(() => {
        if (searchQuery.length < 2) {
            setSearchResults([]);
            return;
        }

        const timer = setTimeout(() => {
            setIsSearching(true);
            fetch(`/api/tenants/search?q=${encodeURIComponent(searchQuery)}`)
                .then(res => res.json())
                .then(data => {
                    setSearchResults(data);
                    setIsSearching(false);
                })
                .catch(() => setIsSearching(false));
        }, 300);

        return () => clearTimeout(timer);
    }, [searchQuery]);

    const handleClose = () => {
        setSearchQuery('');
        setSearchResults([]);
        setSelectedTenantId(null);
        setSelectedTransferBedId('');
        setRentAmount('');
        onClose();
    };

    const handleAssign = () => {
        if (!bed || !selectedTenantId) return;

        router.post(`/beds/${bed.id}/assign`, {
            tenant_id: selectedTenantId,
            rent_amount: rentAmount || bed.monthly_rate
        }, {
            onSuccess: () => {
                toast.success('Tenant assigned successfully.');
                handleClose();
            },
            onError: (errors) => {
                toast.error(errors.error || 'Failed to assign tenant.');
            }
        });
    };

    const handleCheckout = () => {
        if (!bed || !confirm('Are you sure you want to check out this tenant?')) return;

        router.post(`/beds/${bed.id}/checkout`, {}, {
            onSuccess: () => {
                toast.success('Tenant checked out.');
                handleClose();
            },
            onError: (errors) => {
                toast.error(errors.error || 'Failed to check out.');
            }
        });
    };

    const handleTransfer = () => {
        if (!bed || !selectedTransferBedId) return;

        router.post(`/beds/${bed.id}/transfer`, {
            new_bed_id: selectedTransferBedId
        }, {
            onSuccess: () => {
                toast.success('Tenant transferred successfully.');
                handleClose();
            },
            onError: (errors) => {
                toast.error(errors.error || 'Failed to transfer.');
            }
        });
    };

    if (!bed) return null;

    const currentBooking = bed.current_assignment?.booking;
    const currentTenant = currentBooking?.tenant;

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && handleClose()}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Bed {bed.name}</DialogTitle>
                    <DialogDescription className="text-sm">
                        {bed.status === 'available' ? 'Assign a tenant to this available bed.' : 'Manage current occupancy.'}
                    </DialogDescription>
                </DialogHeader>

                {bed.status === 'available' && (
                    <div className="space-y-6">
                        <div className="space-y-4 border rounded-md p-4 bg-muted/30">
                            <h3 className="font-medium flex items-center gap-2">
                                <UserPlus className="h-4 w-4" /> New Tenant
                            </h3>
                            <p className="text-sm text-muted-foreground">Register a new tenant and assign them to this bed automatically.</p>
                            {/* Assuming a create tenant route exists. If not, it will just 404 until implemented */}
                            <Button asChild className="w-full">
                                <Link href={`/tenants/create?bed_id=${bed.id}`}>
                                    Create & Assign New Tenant
                                </Link>
                            </Button>
                        </div>

                        <div className="space-y-4 border rounded-md p-4">
                            <h3 className="font-medium flex items-center gap-2">
                                <Search className="h-4 w-4" /> Returning Tenant
                            </h3>
                            <div className="space-y-2">
                                <Label>Search Existing Tenant</Label>
                                <Input 
                                    placeholder="Search by name or phone..." 
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                                {isSearching && <Loader2 className="h-4 w-4 animate-spin text-muted-foreground" />}
                                
                                {searchResults.length > 0 && !selectedTenantId && (
                                    <div className="border rounded-md mt-2 max-h-40 overflow-y-auto bg-background">
                                        {searchResults.map(t => (
                                            <div 
                                                key={t.id} 
                                                className="p-2 hover:bg-muted cursor-pointer text-sm"
                                                onClick={() => {
                                                    setSelectedTenantId(t.id);
                                                    setSearchQuery(t.full_name || t.name);
                                                    setSearchResults([]);
                                                }}
                                            >
                                                {t.full_name || t.name} <span className="text-muted-foreground">({t.phone})</span>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            {selectedTenantId && (
                                <div className="space-y-2 pt-2">
                                    <Label>Monthly Rent Amount (रु)</Label>
                                    <Input 
                                        type="number"
                                        placeholder={bed.monthly_rate.toString()}
                                        value={rentAmount}
                                        onChange={(e) => setRentAmount(e.target.value)}
                                    />
                                    <p className="text-xs text-muted-foreground">Defaults to bed's standard rate if left empty.</p>
                                    
                                    <div className="flex flex-col sm:flex-row gap-2 pt-2">
                                        <Button variant="outline" className="w-full sm:w-1/2" onClick={() => {
                                            setSelectedTenantId(null);
                                            setSearchQuery('');
                                        }}>
                                            Cancel
                                        </Button>
                                        <Button className="w-full sm:w-1/2" onClick={handleAssign}>
                                            Assign
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {bed.status === 'occupied' && currentTenant && (
                    <div className="space-y-6">
                        <div className="flex items-center gap-4 p-4 border rounded-md bg-muted/30">
                            <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                {(currentTenant.full_name || currentTenant.name).charAt(0)}
                            </div>
                            <div>
                                <h3 className="font-semibold text-lg">{currentTenant.full_name || currentTenant.name}</h3>
                                <div className="text-sm text-muted-foreground flex items-center gap-2">
                                    <span>{currentTenant.phone || 'No phone'}</span>
                                    {currentBooking?.check_in_date && (
                                        <span>• Since {currentBooking.check_in_date}</span>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <Button variant="destructive" className="w-full flex items-center justify-center gap-2" onClick={handleCheckout}>
                                <LogOut className="h-4 w-4" /> Check Out
                            </Button>
                            <Button variant="outline" asChild className="w-full flex items-center justify-center gap-2">
                                <Link href={`/tenants/${currentTenant.id}`}>
                                    <User className="h-4 w-4" /> View Profile
                                </Link>
                            </Button>
                        </div>

                        <div className="pt-4 border-t space-y-4">
                            <h3 className="font-medium flex items-center gap-2">
                                <ArrowRightLeft className="h-4 w-4" /> Transfer Bed
                            </h3>
                            <div className="flex flex-col sm:flex-row gap-2">
                                <div className="flex-1 w-full">
                                    <Select value={selectedTransferBedId} onValueChange={setSelectedTransferBedId}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select available bed" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {availableBeds.map(b => (
                                                <SelectItem key={b.id} value={b.id.toString()}>
                                                    Bed {b.name} ({b.room_name})
                                                </SelectItem>
                                            ))}
                                            {availableBeds.length === 0 && (
                                                <SelectItem value="none" disabled>No available beds</SelectItem>
                                            )}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <Button className="w-full sm:w-auto" onClick={handleTransfer} disabled={!selectedTransferBedId}>
                                    Transfer
                                </Button>
                            </div>
                        </div>
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
