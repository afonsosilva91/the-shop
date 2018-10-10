import React, { Component } from "react";
import { Route } from 'react-router-dom'

import Grid from '@material-ui/core/Grid';
import Paper from '@material-ui/core/Paper';

import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Button from '@material-ui/core/Button';

 
class OrderDetails extends Component {
    constructor () {
        super()

        this.state = {
          order: []
        }
    }

    render() {
        return (
            <div className="order-details">
                <Grid container direction="row" justify="center" alignItems="center" xs={12}>
                    <Paper>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>#ID</TableCell>
                                    <TableCell>Customer</TableCell>
                                    <TableCell>Order</TableCell>
                                    <TableCell>Discount</TableCell>
                                    <TableCell>Total</TableCell>
                                    <TableCell>Date</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {this.state.order.map(order => {
                                    return (
                                        <TableRow key={order.id}>
                                            <TableCell>{order.id}</TableCell>
                                            <TableCell>({order.customer.id}) {order.customer.name}</TableCell>
                                            <TableCell>{order.total_order}</TableCell>
                                            <TableCell>{order.total_discount}</TableCell>
                                            <TableCell>{order.total}</TableCell>
                                            <TableCell>{order.date}</TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    </Paper>
                </Grid>

                <div class="box-actions">
                    <Route render={({ history }) => (
                        <Button variant="contained" color="default" onClick={() => { history.push('/') }}>Back</Button>
                        
                    )} />
                    <Button variant="contained" color="primary">Editar</Button>    
                </div>
                
            </div>
        );
    }
}
 
export default OrderDetails;